"""Tests for NSL-KDD data loader."""
import unittest
import numpy as np
import os
import tempfile
import pandas as pd
from src.data.nsl_kdd import NSLKDDLoader

class TestNSLKDDLoader(unittest.TestCase):
    
    def setUp(self):
        """Create a minimal mock NSL-KDD file for testing."""
        self.loader = NSLKDDLoader()
        # Create temp file with valid NSL-KDD format rows
        self.tmp = tempfile.NamedTemporaryFile(
            mode='w', suffix='.txt', delete=False
        )
        # Write 10 rows of valid NSL-KDD format
        for i in range(10):
            label = ['normal', 'neptune', 'ipsweep', 'warezclient', 'rootkit'][i % 5]
            row = ','.join(['0'] * 41) + f',{label},20\n'
            self.tmp.write(row)
        self.tmp.close()
    
    def tearDown(self):
        if os.path.exists(self.tmp.name):
            os.unlink(self.tmp.name)
    
    def test_load_returns_dataframe(self):
        """load() must return a DataFrame with correct shape."""
        df = self.loader.load(self.tmp.name)
        self.assertIsInstance(df, pd.DataFrame)
        self.assertGreater(len(df), 0)
    
    def test_preprocess_returns_arrays(self):
        """preprocess() must return numpy arrays."""
        df = self.loader.load(self.tmp.name)
        X, y = self.loader.preprocess(df)
        self.assertIsInstance(X, np.ndarray)
        self.assertIsInstance(y, np.ndarray)
        self.assertEqual(len(X), len(y))
    
    def test_label_encoding_five_classes(self):
        """Labels must map to 5 classes: Normal, DoS, Probe, R2L, U2R."""
        df = self.loader.load(self.tmp.name)
        X, y = self.loader.preprocess(df)
        unique_labels = np.unique(y)
        # All encoded labels must be 0-4
        self.assertTrue(np.all(unique_labels >= 0))
        self.assertTrue(np.all(unique_labels <= 4))

if __name__ == "__main__":
    unittest.main()
