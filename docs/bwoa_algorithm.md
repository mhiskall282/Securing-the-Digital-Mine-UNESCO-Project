# Binary Whale Optimization Algorithm (BWOA)

This document details the mathematical model and search flowchart for the Binary Whale Optimization Algorithm (BWOA) used for feature subset selection.

---

## 1. Flowchart
The optimization lifecycle runs iteratively through encircling, exploration, and bubble-net search mechanisms:

```mermaid
flowchart LR
    subgraph P1["Phase 1: Swarm Initialization"]
        direction TB
        A1["🐋 <b>Initialize 30 Whales</b><br/>Random bitmasks in {0,1}^41"]
        A2["📊 <b>Evaluate Agent Fitness</b><br/>Classification error + feature ratio"]
        A3["👑 <b>Identify Leader X_best</b><br/>Optimal feature subset so far"]
        A1 --> A2 --> A3
    end

    subgraph P2["Phase 2: Search Dynamics (Iteration t)"]
        direction TB
        B1["🧭 <b>Update Parameter a</b><br/>Linear decay 2 ➔ 0"]
        B2{"Search Decision"}
        B3["🎯 <b>Encircling Prey (|A|<1)</b><br/>Shrinking search radius"]
        B4["🔍 <b>Random Exploration (|A|>=1)</b><br/>Global stochastic search"]
        B5["🌀 <b>Spiral Bubble-Net (p>=0.5)</b><br/>Logarithmic spiral path"]
        B1 --> B2
        B2 -- "p < 0.5, |A| < 1" --> B3
        B2 -- "p < 0.5, |A| >= 1" --> B4
        B2 -- "p >= 0.5" --> B5
    end

    subgraph P3["Phase 3: V-Shaped Binarization & Output"]
        direction TB
        C1["📐 <b>V-Shaped Transfer</b><br/>V(v) = |v / sqrt(1 + v^2)|"]
        C2["🎲 <b>Probabilistic Bit Flip</b><br/>Discretize continuous velocity"]
        C3["⚖️ <b>Accuracy Floor Gate</b><br/>Penalty 1.0 if Acc < 75%"]
        C4["🏆 <b>10-Feature Mask Output</b><br/>Convergence at Iteration 23"]
        C1 --> C2 --> C3 --> C4
    end

    A3 ==> B1
    B3 & B4 & B5 ==> C1

    classDef b1 fill:#1e293b,stroke:#38bdf8,stroke-width:2px,color:#f8fafc;
    classDef b2 fill:#1e293b,stroke:#f59e0b,stroke-width:2px,color:#f8fafc;
    classDef b3 fill:#1e293b,stroke:#10b981,stroke-width:2px,color:#f8fafc;

    class A1,A2,A3 b1;
    class B1,B2,B3,B4,B5 b2;
    class C1,C2,C3,C4 b3;
```

---

## 2. Mathematical Formulation

The BWOA models the social behaviors of humpback whales using three distinct movement mechanisms:

---

### 2.1 Encircling Prey

Whales identify the location of prey and encircle them. The distance to the best solution is first computed, then used to update the agent's position:

$$\mathbf{D} = \left| \mathbf{C} \cdot \mathbf{X}^{*}(t) - \mathbf{X}(t) \right|$$

$$\mathbf{X}(t+1) = \mathbf{X}^{*}(t) - \mathbf{A} \cdot \mathbf{D}$$

The coefficient vectors $\mathbf{A}$ and $\mathbf{C}$ are defined as:

$$\mathbf{A} = 2a\mathbf{r} - a \qquad \mathbf{C} = 2\mathbf{r}$$

| Symbol | Description |
|--------|-------------|
| $t$ | Current iteration |
| $\mathbf{X}^{*}(t)$ | Position vector of the best (leader) solution found so far |
| $\mathbf{X}(t)$ | Position vector of the current search agent |
| $a$ | Control parameter, decreases linearly from $2 \to 0$ over iterations |
| $\mathbf{r}$ | Random vector, each element drawn from $\mathcal{U}[0,\,1]$ |

---

### 2.2 Bubble-Net Attack (Spiral Update)

Whales simultaneously shrink the encircling circle and follow a logarithmic spiral path around the prey. The two mechanisms are selected with equal probability $p \sim \mathcal{U}[0,1]$:

$$\mathbf{X}(t+1) = \begin{cases} \mathbf{X}^{*}(t) - \mathbf{A} \cdot \mathbf{D} & \text{if } p < 0.5 \\[6pt] \mathbf{D}' \cdot e^{\,bl} \cdot \cos(2\pi l) + \mathbf{X}^{*}(t) & \text{if } p \ge 0.5 \end{cases}$$

where the spiral distance $\mathbf{D}'$ and the spiral update are given by:

$$\mathbf{D}' = \left| \mathbf{X}^{*}(t) - \mathbf{X}(t) \right|$$

$$\mathbf{X}(t+1) = \mathbf{D}' \cdot e^{\,bl} \cdot \cos(2\pi l) + \mathbf{X}^{*}(t)$$

| Symbol | Description |
|--------|-------------|
| $\mathbf{D}'$ | Distance between the current whale and the prey |
| $b$ | Constant defining the logarithmic spiral shape |
| $l$ | Random number drawn from $\mathcal{U}[-1,\,1]$ |
| $p$ | Random probability value drawn from $\mathcal{U}[0,\,1]$ |

---

### 2.3 Exploration (Search for Prey)

When $|\mathbf{A}| \ge 1$, whales deviate from the current best agent and perform a global stochastic search guided by a randomly selected agent $\mathbf{X}_{\text{rand}}$:

$$\mathbf{D} = \left| \mathbf{C} \cdot \mathbf{X}_{\text{rand}} - \mathbf{X}(t) \right|$$

$$\mathbf{X}(t+1) = \mathbf{X}_{\text{rand}} - \mathbf{A} \cdot \mathbf{D}$$

| Symbol | Description |
|--------|-------------|
| $\mathbf{X}_{\text{rand}}$ | Position vector of a randomly selected agent in the current population |
| $\mathbf{A}$, $\mathbf{C}$ | Coefficient vectors (same as §2.1) |

> **Exploration vs. Exploitation switch**: When $|\mathbf{A}| \ge 1$ the algorithm explores (global search); when $|\mathbf{A}| < 1$ it exploits (local refinement around $\mathbf{X}^{*}$).

---

## 3. Binary Adaptation and V-Shaped Transfer Function

To apply the Whale Optimization Algorithm to discrete feature selection (binary space), continuous position changes are mapped to probability thresholds. We utilize a V-shaped transfer function to convert continuous step updates $\mathbf{V}$ to probability mappings:

$$T(v) = \left| \frac{2}{\pi} \arctan\left(\frac{\pi}{2} v\right) \right|$$

Alternatively, the V-shaped mapping is calculated as:
$$T(v) = \left| \frac{v}{\sqrt{1 + v^2}} \right|$$

The position of each search agent is updated by comparing the probability $T(v)$ to a random threshold $r \in [0, 1]$:
$$x_{i,j}(t+1) = \begin{cases} 1 - x_{i,j}(t) & \text{if } r < T(v_{i,j}(t+1)) \\ x_{i,j}(t) & \text{otherwise} \end{cases}$$

---

## 4. Fitness Function Formulation

The feature selection wrapper optimizes a multi-objective function, maximizing classification performance while minimizing the feature count:

$$\text{Fitness} = \alpha \times \left( \frac{N_{\text{selected}}}{N_{\text{total}}} \right) + (1 - \alpha) \times \text{Error Rate}$$

Where:
* $\text{Error Rate} = 1 - \text{Accuracy}$ on 3-fold stratified cross validation splits (using RandomForest proxy).
* $N_{\text{selected}}$ is the number of active features in the current mask.
* $N_{\text{total}}$ is the total number of features (e.g., 41 for NSL-KDD).
* $\alpha \in [0, 1]$ is a weight parameter. In v3, $\alpha = 0.3$ to prioritize classification accuracy (70%) over feature reduction (30%).

**Accuracy Floor Constraint**: Any feature subset yielding validation accuracy below the threshold $\tau$ (default: $\tau = 0.75$) is assigned the maximum fitness penalty of 1.0 and rejected:

$$\text{Fitness} = \begin{cases} 1.0 & \text{if Accuracy} < \tau \text{ (floor constraint)} \\ \alpha \times \frac{N_{\text{selected}}}{N_{\text{total}}} + (1-\alpha) \times (1 - \text{Accuracy}) & \text{otherwise} \end{cases}$$
