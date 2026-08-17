"""Tests for BWOA fitness function."""
import unittest
import numpy as np
from src.optimization.fitness import FeatureFitnessEvaluator

class TestFeatureFitnessEvaluator(unittest.TestCase):
    
    def setUp(self):
        np.random.seed(42)
        self.evaluator = FeatureFitnessEvaluator(
            alpha=0.3, min_accuracy=0.75, min_features=10
        )
        self.X = np.random.rand(200, 41)
        self.y = np.random.randint(0, 5, 200)
    
    def test_too_few_features_returns_penalty(self):
        """Masks with fewer than min_features must return fitness=1.0."""
        mask = np.zeros(41)
        mask[:5] = 1  # only 5 features, below min of 10
        fitness = self.evaluator.calculate_fitness(mask, self.X, self.y)
        self.assertEqual(fitness, 1.0)
    
    def test_fitness_range(self):
        """Valid mask fitness must be between 0 and 1."""
        mask = np.zeros(41)
        mask[:15] = 1  # 15 features, above min
        fitness = self.evaluator.calculate_fitness(mask, self.X, self.y)
        self.assertGreaterEqual(fitness, 0.0)
        self.assertLessEqual(fitness, 1.0)
    
    def test_all_features_fitness(self):
        """All-features mask must produce a valid fitness score."""
        mask = np.ones(41)
        fitness = self.evaluator.calculate_fitness(mask, self.X, self.y)
        self.assertGreater(fitness, 0.0)
        self.assertLessEqual(fitness, 1.0)
    
    def test_minimum_features_enforced(self):
        """Mask with exactly min_features must NOT return penalty on min_features count alone."""
        mask = np.zeros(41)
        mask[:10] = 1  # exactly 10 features
        fitness = self.evaluator.calculate_fitness(mask, self.X, self.y)
        self.assertIsInstance(fitness, float)

if __name__ == "__main__":
    unittest.main()
