"""Tests for edge deployment benchmarking."""
import unittest
import numpy as np
from src.evaluation.edge_benchmark import EdgeBenchmark

class TestEdgeBenchmark(unittest.TestCase):
    
    def test_initialization(self):
        """EdgeBenchmark initializes with correct constraints."""
        bench = EdgeBenchmark(target_latency_ms=100.0, max_ram_mb=1024.0)
        self.assertEqual(bench.target_latency_ms, 100.0)
        self.assertEqual(bench.max_ram_mb, 1024.0)
    
    def test_benchmark_memory_returns_dict(self):
        """benchmark_memory() must return a dict with required keys."""
        bench = EdgeBenchmark()
        result = bench.benchmark_memory()
        self.assertIsInstance(result, dict)
        self.assertIn('peak_mb', result)
    
    def test_deployment_readiness_pass(self):
        """PASS verdict when latency and RAM are within limits."""
        bench = EdgeBenchmark(target_latency_ms=100.0, max_ram_mb=1024.0)
        latency = {'mean_ms': 0.76, 'p95_ms': 1.10}
        memory = {'peak_mb': 290.0, 'model_size_mb': 0.82}
        is_ready, report = bench.check_deployment_readiness(latency, memory)
        self.assertTrue(is_ready)
        self.assertIn("READY", report)
    
    def test_deployment_readiness_fail_latency(self):
        """FAIL verdict when latency exceeds target."""
        bench = EdgeBenchmark(target_latency_ms=100.0, max_ram_mb=1024.0)
        latency = {'mean_ms': 157.66, 'p95_ms': 256.23}
        memory = {'peak_mb': 290.0, 'model_size_mb': 1.86}
        is_ready, report = bench.check_deployment_readiness(latency, memory)
        self.assertFalse(is_ready)
        self.assertIn("FAILED", report)

if __name__ == "__main__":
    unittest.main()
