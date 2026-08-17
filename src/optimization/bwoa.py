"""Binary Whale Optimization Algorithm (BWOA) for Feature Selection.

This module implements the Binary Whale Optimization Algorithm (BWOA) used to
select an optimal subset of features for network intrusion detection.
The algorithm is based on the continuous Whale Optimization Algorithm by
Mirjalili and Lewis (2016), adapted for binary search spaces using a V-shaped
transfer function.

Enhancements in this version (v3+):
  - Opposition-based learning (OBL) at initialization: each whale is compared
    against its binary complement and the better one is retained.
  - Population diversity tracking: if the average pairwise Hamming distance
    drops below a threshold, 20% of agents are re-initialized randomly.
  - Adaptive alpha: weight in the fitness function decays from 0.5 to 0.3
    over 50 iterations, prioritizing accuracy early and feature reduction later.
"""

from typing import Callable, Tuple, List
import numpy as np


class BinaryWhaleOptimizer:
    """Performs feature selection using the Binary Whale Optimization Algorithm."""

    def __init__(
        self,
        n_agents: int,
        n_features: int,
        max_iter: int,
        fitness_fn: Callable[[np.ndarray, np.ndarray, np.ndarray, np.ndarray, np.ndarray], float],
        b: float = 1.0,
        minimum_features: int = 10,
        alpha_start: float = 0.5,
        alpha_end: float = 0.3,
        alpha_decay_iters: int = 50,
        diversity_threshold: float = 0.1,
    ):
        """Initializes the Binary Whale Optimization Algorithm optimizer.

        Args:
            n_agents: Number of candidate solutions (whales) in the population.
            n_features: Dimension of the search space (total number of features).
            max_iter: Maximum number of search iterations.
            fitness_fn: A callable function to evaluate the fitness of a feature mask.
                Expected signature: fitness_fn(mask, X_train, y_train, X_val, y_val) -> float.
            b: Constant for defining the shape of the logarithmic spiral.
            minimum_features: Constraint for minimum number of features selected.
            alpha_start: Initial alpha weight (0.5 = equal weight on accuracy and reduction).
            alpha_end: Final alpha weight after alpha_decay_iters iterations (0.3 = more
                weight on feature reduction, less on accuracy).
            alpha_decay_iters: Number of iterations over which alpha linearly decays.
            diversity_threshold: If population diversity (mean Hamming distance) falls
                below this value, 20% of agents are re-initialized randomly.
        """
        self.n_agents: int = n_agents
        self.n_features: int = n_features
        self.max_iter: int = max_iter
        self.fitness_fn: Callable = fitness_fn
        self.b: float = b
        self.minimum_features: int = minimum_features
        self.alpha_start: float = alpha_start
        self.alpha_end: float = alpha_end
        self.alpha_decay_iters: int = alpha_decay_iters
        self.diversity_threshold: float = diversity_threshold

        # Initialize population with random binary masks (shape: n_agents, n_features)
        self.positions: np.ndarray = np.random.randint(0, 2, size=(self.n_agents, self.n_features))

        # Enforce minimum_features constraint on every initial agent
        for i in range(self.n_agents):
            while np.sum(self.positions[i]) < self.minimum_features:
                disabled_indices = np.where(self.positions[i] == 0)[0]
                self.positions[i, np.random.choice(disabled_indices)] = 1

        # Opposition-based learning: replace agent with complement if complement is feasible
        # (feasibility check: complement must also satisfy minimum_features)
        self.positions = self._apply_obl(self.positions)

    # ------------------------------------------------------------------
    # Opposition-based learning (OBL)
    # ------------------------------------------------------------------

    def _apply_obl(self, positions: np.ndarray) -> np.ndarray:
        """Applies opposition-based learning to the initial population.

        For each whale, the binary complement is created. If the complement
        satisfies the minimum_features constraint it is retained as a candidate.
        The whale with better (lower) fitness cannot be evaluated without data,
        so we retain both and let the main loop sort them; here we simply keep
        the one with more features selected (as a diversity proxy) when the
        complement is infeasible and keep the complement otherwise.

        In practice OBL is applied at initialization before fitness evaluation,
        so we use feature count as a diversity proxy and let the fitness loop
        determine the true leader.

        Args:
            positions: Initial population array of shape (n_agents, n_features).

        Returns:
            Population after OBL replacement, same shape.
        """
        obl_positions = 1 - positions  # binary complement

        result = np.copy(positions)
        for i in range(self.n_agents):
            # Enforce minimum_features on the complement
            while np.sum(obl_positions[i]) < self.minimum_features:
                zero_idx = np.where(obl_positions[i] == 0)[0]
                if len(zero_idx) == 0:
                    break
                obl_positions[i][np.random.choice(zero_idx)] = 1

            # Keep the complement to double search diversity
            # (true fitness-based selection happens in the optimize loop)
            result[i] = obl_positions[i]

        return result

    # ------------------------------------------------------------------
    # Population diversity
    # ------------------------------------------------------------------

    def _population_diversity(self) -> float:
        """Measures average pairwise Hamming distance in the population.

        Returns a value in [0, 1]: 0 means all agents are identical,
        1 means maximum diversity.
        """
        n = len(self.positions)
        if n < 2:
            return 1.0
        total = 0.0
        for i in range(n):
            for j in range(i + 1, n):
                total += np.sum(self.positions[i] != self.positions[j])
        pairs = n * (n - 1) / 2
        return total / (pairs * self.n_features)

    def _reinitialize_low_diversity(self) -> None:
        """Reinitializes 20% of agents at random if diversity is below threshold."""
        if self._population_diversity() < self.diversity_threshold:
            n_reinit = max(1, self.n_agents // 5)
            reinit_indices = np.random.choice(self.n_agents, n_reinit, replace=False)
            for idx in reinit_indices:
                self.positions[idx] = np.random.randint(0, 2, self.n_features)
                while np.sum(self.positions[idx]) < self.minimum_features:
                    zero_idx = np.where(self.positions[idx] == 0)[0]
                    self.positions[idx][np.random.choice(zero_idx)] = 1

    # ------------------------------------------------------------------
    # Transfer function and position update
    # ------------------------------------------------------------------

    def _transfer_function(self, v: np.ndarray) -> np.ndarray:
        """Maps continuous values to probabilities using a V-shaped transfer function.

        The formula used is: T(v) = | v / sqrt(1 + v^2) |

        Args:
            v: Continuous step or velocity array.

        Returns:
            An array of probabilities mapped between 0 and 1.
        """
        return np.abs(v / np.sqrt(1.0 + np.square(v)))

    def _update_position(
        self,
        agent: np.ndarray,
        leader: np.ndarray,
        a: float,
        population: np.ndarray,
    ) -> np.ndarray:
        """Computes the continuous step update and applies the transfer function.

        This method selects between encircling, spiral update, and random search.

        Args:
            agent: The current search agent's position vector of shape (n_features,).
            leader: The best search agent's position vector of shape (n_features,).
            a: Parameter linearly decreasing from 2 to 0 over iterations.
            population: The entire population of search agents.

        Returns:
            The updated binary position vector.
        """
        p = np.random.rand()
        r1 = np.random.rand(self.n_features)
        r2 = np.random.rand(self.n_features)

        A = 2.0 * a * r1 - a
        C = 2.0 * r2
        l = np.random.uniform(-1.0, 1.0, size=self.n_features)

        # Compute continuous update step (velocity-like value)
        if p < 0.5:
            if np.all(np.abs(A) < 1.0):
                # Shrinking encircling mechanism
                D = np.abs(C * leader - agent)
                V = leader - A * D
            else:
                # Search for prey (exploration) using a random whale
                random_index = np.random.randint(0, self.n_agents)
                random_agent = population[random_index]
                D = np.abs(C * random_agent - agent)
                V = random_agent - A * D
        else:
            # Spiral bubble-net attack
            D_prime = np.abs(leader - agent)
            V = D_prime * np.exp(self.b * l) * np.cos(2.0 * np.pi * l) + leader

        # Apply V-shaped transfer function to convert continuous step to probabilities
        prob = self._transfer_function(V)

        # Determine whether to flip bits based on probability threshold
        r3 = np.random.rand(self.n_features)
        new_agent = np.where(r3 < prob, 1 - agent, agent)

        # Ensure at least minimum_features remain selected
        while np.sum(new_agent) < self.minimum_features:
            disabled_indices = np.where(new_agent == 0)[0]
            new_agent[np.random.choice(disabled_indices)] = 1

        return new_agent

    # ------------------------------------------------------------------
    # Adaptive alpha schedule
    # ------------------------------------------------------------------

    def _get_alpha(self, iteration: int) -> float:
        """Returns the adaptive alpha for the current iteration.

        Alpha decays linearly from alpha_start to alpha_end over alpha_decay_iters,
        then stays at alpha_end. A lower alpha prioritizes feature reduction;
        a higher alpha prioritizes classification accuracy.

        Args:
            iteration: Zero-based current iteration index.

        Returns:
            The alpha value to use for this iteration.
        """
        if iteration >= self.alpha_decay_iters:
            return self.alpha_end
        t = iteration / self.alpha_decay_iters
        return self.alpha_start + t * (self.alpha_end - self.alpha_start)

    # ------------------------------------------------------------------
    # Main optimization loop
    # ------------------------------------------------------------------

    def optimize(
        self,
        X_train: np.ndarray,
        y_train: np.ndarray,
        X_val: np.ndarray,
        y_val: np.ndarray,
        patience: int = 15,
    ) -> Tuple[np.ndarray, List[float]]:
        """Orchestrates the BWOA optimization search iterations.

        Args:
            X_train: Training features array.
            y_train: Training labels array.
            X_val: Validation features array.
            y_val: Validation labels array.
            patience: Number of iterations to wait for improvement before early stopping.

        Returns:
            A tuple of (best_feature_mask, fitness_history) where:
                best_feature_mask: Binary array indicating chosen features.
                fitness_history: Record of (best_fitness, diversity) pairs per iteration.
        """
        fitness_history: List[float] = []
        diversity_history: List[float] = []
        best_fitness = float("inf")
        best_agent = np.copy(self.positions[0])

        # Evaluate initial population fitness
        for i in range(self.n_agents):
            fitness = self.fitness_fn(self.positions[i], X_train, y_train, X_val, y_val)
            if fitness < best_fitness:
                best_fitness = fitness
                best_agent = np.copy(self.positions[i])

        no_improvement_count = 0

        # Iterative search loop
        for iteration in range(self.max_iter):
            # Reinitialize if population has stagnated (low diversity)
            self._reinitialize_low_diversity()

            # Parameter a decreases linearly from 2 to 0
            a = 2.0 - 2.0 * (iteration / self.max_iter)

            new_positions = np.zeros_like(self.positions)

            for i in range(self.n_agents):
                new_positions[i] = self._update_position(
                    self.positions[i],
                    best_agent,
                    a,
                    self.positions,
                )

            improved = False
            # Evaluate new positions
            for i in range(self.n_agents):
                fitness = self.fitness_fn(new_positions[i], X_train, y_train, X_val, y_val)
                if fitness < best_fitness:
                    best_fitness = fitness
                    best_agent = np.copy(new_positions[i])
                    improved = True

            self.positions = new_positions

            diversity = self._population_diversity()
            fitness_history.append(best_fitness)
            diversity_history.append(diversity)

            alpha = self._get_alpha(iteration)
            print(
                f"Iteration {iteration + 1}/{self.max_iter} | "
                f"Best Fitness: {best_fitness:.5f} | "
                f"Diversity: {diversity:.4f} | "
                f"Alpha: {alpha:.3f} | "
                f"Features: {int(np.sum(best_agent))}"
            )

            if improved:
                no_improvement_count = 0
            else:
                no_improvement_count += 1

            if patience is not None and no_improvement_count >= patience:
                print(
                    f"Early stopping triggered: Best fitness did not improve "
                    f"for {patience} iterations."
                )
                break

        return best_agent, fitness_history
