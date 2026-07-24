"""Unit tests for the BATADAL data loader and preprocessing module.
"""

import os
import sys
import tempfile
import unittest
import warnings

import numpy as np
import pandas as pd

PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
if PROJECT_ROOT not in sys.path:
    sys.path.insert(0, PROJECT_ROOT)

from src.data.batadal import BATADAL_FEATURE_NAMES, BATADALLoader


class TestBATADALLoader(unittest.TestCase):
    def setUp(self) -> None:
        self.tmp = tempfile.mkdtemp()
        self.mock_path = os.path.join(self.tmp, "batadal_mock.csv")
        self.loader = BATADALLoader()

    def tearDown(self) -> None:
        if os.path.exists(self.mock_path):
            os.remove(self.mock_path)
        os.rmdir(self.tmp)

    def test_mock_created_when_missing(self) -> None:
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            df = self.loader.load(self.mock_path)
        self.assertTrue(os.path.exists(self.mock_path))
        self.assertEqual(len(df), 3000)

    def test_preprocess_shapes(self) -> None:
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            df = self.loader.load(self.mock_path)
        X, y = self.loader.preprocess(df)
        self.assertEqual(X.shape, (3000, 43))
        self.assertEqual(y.shape, (3000,))

    def test_sliding_window(self) -> None:
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            df = self.loader.load(self.mock_path)
        X, y = self.loader.preprocess(df)
        X_w, y_w = self.loader.sliding_window(X, y, window_size=10)
        self.assertEqual(X_w.shape, (2991, 10, 43))
        self.assertEqual(y_w.shape, (2991,))


if __name__ == "__main__":
    unittest.main()
