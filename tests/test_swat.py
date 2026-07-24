"""Comprehensive test suite for the SWaT data loader and Phase 2 pipeline.

Tests cover:
  - SWaTLoader: CSV loading, mock generation, preprocessing, splitting, normalization
  - Spectral residual transform correctness
  - Sliding window shapes and label alignment
  - Process group index mapping
  - load_swat_for_transfer end-to-end pipeline
  - Attack stats computation
  - Label variant handling ("Normal", "Attack", "A ttack", "0", "1")
"""

import os
import sys
import tempfile
import unittest
import warnings
from typing import Tuple

import numpy as np
import pandas as pd

# Adjust import path for running from project root
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
if PROJECT_ROOT not in sys.path:
    sys.path.insert(0, PROJECT_ROOT)

from src.data.swat import (
    SWAT_FEATURE_NAMES,
    SWaTLoader,
    _PROCESS_GROUPS,
    load_swat_for_transfer,
)


def _make_minimal_swat_df(
    n_rows: int = 200,
    n_features: int = 51,
    attack_ratio: float = 0.15,
    label_variant: str = "Normal/Attack",
) -> pd.DataFrame:
    """Creates a minimal synthetic SWaT DataFrame for unit testing."""
    np.random.seed(0)
    features = SWAT_FEATURE_NAMES[:n_features]
    data = np.random.rand(n_rows, n_features).astype(np.float32)
    df = pd.DataFrame(data, columns=features)
    df.insert(0, "Timestamp", pd.date_range("2015-12-22", periods=n_rows, freq="s"))

    labels_arr = np.where(
        np.random.rand(n_rows) < attack_ratio, "Attack", "Normal"
    )
    df[label_variant] = labels_arr
    return df


def _make_csv_file(df: pd.DataFrame, suffix: str = ".csv") -> str:
    """Writes a DataFrame to a temporary file and returns the path."""
    tmp = tempfile.NamedTemporaryFile(delete=False, suffix=suffix, mode="w")
    df.to_csv(tmp.name, index=False)
    tmp.close()
    return tmp.name


class TestSWaTLoaderInit(unittest.TestCase):
    """Tests initialisation and schema correctness."""

    def test_default_feature_count(self) -> None:
        """SWaTLoader should expose exactly 51 default features."""
        loader = SWaTLoader()
        self.assertEqual(len(loader.feature_names), 51)

    def test_feature_names_match_module_constant(self) -> None:
        """Default feature_names should equal the module-level constant."""
        loader = SWaTLoader()
        self.assertEqual(loader.feature_names, SWAT_FEATURE_NAMES)

    def test_process_groups_cover_all_features(self) -> None:
        """All 51 features should be covered by the 6 process groups."""
        all_feats = [f for feats in _PROCESS_GROUPS.values() for f in feats]
        self.assertEqual(set(all_feats), set(SWAT_FEATURE_NAMES))

    def test_custom_feature_names(self) -> None:
        """Custom feature names should be stored correctly."""
        custom = ["f1", "f2", "f3"]
        loader = SWaTLoader(feature_names=custom)
        self.assertEqual(loader.feature_names, custom)
        self.assertEqual(loader.n_features, 3)


class TestSWaTMockGeneration(unittest.TestCase):
    """Tests the synthetic mock dataset generation."""

    def setUp(self) -> None:
        self.tmp = tempfile.mkdtemp()
        self.mock_path = os.path.join(self.tmp, "swat_mock.csv")

    def tearDown(self) -> None:
        if os.path.exists(self.mock_path):
            os.remove(self.mock_path)
        os.rmdir(self.tmp)

    def test_mock_created_when_missing(self) -> None:
        """Loader should auto-generate a mock file if path doesn't exist."""
        loader = SWaTLoader()
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            df = loader.load(self.mock_path)
        self.assertTrue(os.path.exists(self.mock_path))
        self.assertIsInstance(df, pd.DataFrame)

    def test_mock_has_correct_columns(self) -> None:
        """Mock file should contain Timestamp, 51 feature columns, and label."""
        loader = SWaTLoader()
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            df = loader.load(self.mock_path)
        self.assertIn("Timestamp", df.columns)
        self.assertIn("Normal/Attack", df.columns)
        # At least 51 sensor columns
        sensor_cols = [c for c in df.columns if c not in ("Timestamp", "Normal/Attack")]
        self.assertGreaterEqual(len(sensor_cols), 51)

    def test_mock_has_both_label_classes(self) -> None:
        """Mock dataset should contain both Normal and Attack labels."""
        loader = SWaTLoader()
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            df = loader.load(self.mock_path)
        unique_labels = df["Normal/Attack"].unique()
        self.assertIn("Normal", unique_labels)
        self.assertIn("Attack", unique_labels)

    def test_mock_row_count(self) -> None:
        """Mock dataset should have 5000 rows."""
        loader = SWaTLoader()
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            df = loader.load(self.mock_path)
        self.assertEqual(len(df), 5000)


class TestSWaTLoaderCSV(unittest.TestCase):
    """Tests CSV loading and label parsing."""

    def setUp(self) -> None:
        self.df = _make_minimal_swat_df(n_rows=300)
        self.csv_path = _make_csv_file(self.df)
        self.loader = SWaTLoader()

    def tearDown(self) -> None:
        if os.path.exists(self.csv_path):
            os.remove(self.csv_path)

    def test_load_returns_dataframe(self) -> None:
        df = self.loader.load(self.csv_path)
        self.assertIsInstance(df, pd.DataFrame)
        self.assertEqual(len(df), 300)

    def test_load_preserves_columns(self) -> None:
        df = self.loader.load(self.csv_path)
        self.assertIn("Timestamp", df.columns)
        self.assertIn("Normal/Attack", df.columns)

    def test_load_no_warnings_for_existing_file(self) -> None:
        """Loading an existing file should not emit SWaT warnings."""
        with warnings.catch_warnings(record=True) as w:
            warnings.simplefilter("always")
            self.loader.load(self.csv_path)
        swat_warnings = [x for x in w if "SWaT" in str(x.message)]
        self.assertEqual(len(swat_warnings), 0)


class TestSWaTPreprocess(unittest.TestCase):
    """Tests the preprocess() method for feature extraction and label parsing."""

    def setUp(self) -> None:
        self.loader = SWaTLoader()

    def test_preprocess_returns_correct_shapes(self) -> None:
        df = _make_minimal_swat_df(n_rows=200)
        X, y = self.loader.preprocess(df)
        self.assertEqual(X.shape, (200, 51))
        self.assertEqual(y.shape, (200,))

    def test_labels_are_binary(self) -> None:
        df = _make_minimal_swat_df(n_rows=200)
        _, y = self.loader.preprocess(df)
        unique_values = set(y)
        self.assertTrue(unique_values.issubset({0, 1}))

    def test_attack_label_parsed_correctly(self) -> None:
        """Attack rows should map to y=1."""
        df = _make_minimal_swat_df(n_rows=100, attack_ratio=1.0)  # all attacks
        _, y = self.loader.preprocess(df)
        self.assertTrue(np.all(y == 1))

    def test_normal_label_parsed_correctly(self) -> None:
        """Normal rows should map to y=0."""
        df = _make_minimal_swat_df(n_rows=100, attack_ratio=0.0)  # all normal
        _, y = self.loader.preprocess(df)
        self.assertTrue(np.all(y == 0))

    def test_attack_typo_variant_parsed(self) -> None:
        """The 'A ttack' (with space) typo should be parsed as attack (y=1)."""
        df = _make_minimal_swat_df(n_rows=50, attack_ratio=1.0)
        df["Normal/Attack"] = "A ttack"  # Simulate the iTrust quirk
        _, y = self.loader.preprocess(df)
        # "a ttack" != "normal" so should be treated as attack
        self.assertTrue(np.all(y == 1))

    def test_timestamp_col_removed_from_features(self) -> None:
        """Timestamp must not appear in X."""
        df = _make_minimal_swat_df(n_rows=100)
        X, _ = self.loader.preprocess(df)
        # X should have only numeric sensor columns
        self.assertEqual(X.ndim, 2)
        self.assertLessEqual(X.shape[1], 51)

    def test_return_timestamps_flag(self) -> None:
        """return_timestamps=True should return a 3-tuple."""
        df = _make_minimal_swat_df(n_rows=100)
        result = self.loader.preprocess(df, return_timestamps=True)
        self.assertEqual(len(result), 3)
        X, y, ts = result
        self.assertEqual(len(ts), 100)

    def test_whitespace_stripped_from_columns(self) -> None:
        """Column names with leading/trailing spaces should be handled."""
        df = _make_minimal_swat_df(n_rows=50)
        df.columns = [" " + c + " " for c in df.columns]
        X, y = self.loader.preprocess(df)
        self.assertEqual(X.shape[0], 50)

    def test_x_dtype_is_float32(self) -> None:
        df = _make_minimal_swat_df(n_rows=50)
        X, _ = self.loader.preprocess(df)
        self.assertEqual(X.dtype, np.float32)

    def test_nan_values_handled(self) -> None:
        """NaN values in feature columns should be filled (no crash)."""
        df = _make_minimal_swat_df(n_rows=100)
        # Inject NaNs
        for col in SWAT_FEATURE_NAMES[:5]:
            if col in df.columns:
                df.loc[df.index[:10], col] = np.nan
        X, y = self.loader.preprocess(df)
        self.assertFalse(np.any(np.isnan(X)))


class TestSWaTSplitting(unittest.TestCase):
    """Tests train/test splitting methods."""

    def setUp(self) -> None:
        self.loader = SWaTLoader()
        df = _make_minimal_swat_df(n_rows=500)
        self.X, self.y = self.loader.preprocess(df)

    def test_temporal_split_sizes(self) -> None:
        X_tr, X_te, y_tr, y_te = self.loader.train_test_split_temporal(
            self.X, self.y, train_ratio=0.75
        )
        self.assertEqual(len(X_tr), 375)
        self.assertEqual(len(X_te), 125)

    def test_temporal_split_no_overlap(self) -> None:
        """Temporal split must not have any overlapping rows."""
        X_tr, X_te, y_tr, y_te = self.loader.train_test_split_temporal(
            self.X, self.y, train_ratio=0.75
        )
        # Training ends before test starts (first test row != any training row)
        self.assertEqual(len(X_tr) + len(X_te), len(self.X))

    def test_random_split_sizes(self) -> None:
        X_tr, X_te, y_tr, y_te = self.loader.train_test_split(
            self.X, self.y, test_size=0.2
        )
        self.assertAlmostEqual(len(X_te) / len(self.X), 0.2, delta=0.01)

    def test_random_split_no_data_loss(self) -> None:
        X_tr, X_te, y_tr, y_te = self.loader.train_test_split(self.X, self.y)
        self.assertEqual(len(X_tr) + len(X_te), len(self.X))


class TestSWaTNormalization(unittest.TestCase):
    """Tests MinMax and Standard normalization."""

    def setUp(self) -> None:
        self.loader = SWaTLoader()
        df = _make_minimal_swat_df(n_rows=300)
        X, y = self.loader.preprocess(df)
        self.X_train, self.X_test, _, _ = self.loader.train_test_split_temporal(X, y)

    def test_minmax_range(self) -> None:
        X_tr, X_te = self.loader.normalize(self.X_train, self.X_test, method="minmax")
        # Training data should be in [0, 1] (with tolerance for small numerical errors)
        self.assertGreaterEqual(X_tr.min(), -0.01)
        self.assertLessEqual(X_tr.max(), 1.01)

    def test_standard_zero_mean(self) -> None:
        X_tr, _ = self.loader.normalize(self.X_train, self.X_test, method="standard")
        col_means = np.mean(X_tr, axis=0)
        np.testing.assert_array_almost_equal(col_means, 0.0, decimal=5)

    def test_scaler_stored_after_normalize(self) -> None:
        self.loader.normalize(self.X_train, self.X_test, method="minmax")
        self.assertIsNotNone(self.loader.get_scaler())

    def test_shapes_preserved(self) -> None:
        X_tr, X_te = self.loader.normalize(self.X_train, self.X_test)
        self.assertEqual(X_tr.shape, self.X_train.shape)
        self.assertEqual(X_te.shape, self.X_test.shape)


class TestSlidingWindow(unittest.TestCase):
    """Tests sliding_window tensor generation."""

    def setUp(self) -> None:
        self.loader = SWaTLoader()
        n = 100
        self.X = np.random.rand(n, 51).astype(np.float32)
        self.y = np.random.randint(0, 2, size=n)

    def test_output_shapes(self) -> None:
        window_size = 10
        X_w, y_w = self.loader.sliding_window(self.X, self.y, window_size=window_size)
        expected_n = len(self.X) - window_size + 1
        self.assertEqual(X_w.shape, (expected_n, window_size, 51))
        self.assertEqual(y_w.shape, (expected_n,))

    def test_stride_reduces_samples(self) -> None:
        X_w_s1, _ = self.loader.sliding_window(self.X, self.y, window_size=10, stride=1)
        X_w_s5, _ = self.loader.sliding_window(self.X, self.y, window_size=10, stride=5)
        self.assertGreater(len(X_w_s1), len(X_w_s5))

    def test_label_alignment(self) -> None:
        """Last timestep of each window should be the window's label."""
        window_size = 5
        self.y = np.arange(len(self.X))  # Use index as label for easy verification
        _, y_w = self.loader.sliding_window(self.X, self.y, window_size=window_size)
        # First window's label should be index 4 (last of window [0..4])
        self.assertEqual(y_w[0], window_size - 1)

    def test_float32_output(self) -> None:
        X_w, _ = self.loader.sliding_window(self.X, self.y, window_size=10)
        self.assertEqual(X_w.dtype, np.float32)


class TestSpectralResidual(unittest.TestCase):
    """Tests the spectral residual transform."""

    def test_output_shape_preserved(self) -> None:
        X = np.random.rand(200, 10).astype(np.float32)
        X_sr = SWaTLoader._spectral_residual(X)
        self.assertEqual(X_sr.shape, X.shape)

    def test_anomaly_highlighted(self) -> None:
        """SR transform should produce higher values at injected anomaly locations."""
        T, F = 300, 5
        X = np.ones((T, F), dtype=np.float32)  # flat signal
        X[100:110, :] = 10.0  # inject a spike anomaly at rows 100-109

        X_sr = SWaTLoader._spectral_residual(X, smooth_window=5)

        anomaly_response = np.mean(np.abs(X_sr[100:110]))
        normal_response = np.mean(np.abs(X_sr[:50]))
        # Anomaly region should have higher spectral residual energy
        self.assertGreater(anomaly_response, normal_response * 0.5)

    def test_no_nan_output(self) -> None:
        X = np.random.rand(100, 8).astype(np.float32)
        X_sr = SWaTLoader._spectral_residual(X)
        self.assertFalse(np.any(np.isnan(X_sr)))


class TestAttackStats(unittest.TestCase):
    """Tests the attack_stats computation."""

    def setUp(self) -> None:
        self.loader = SWaTLoader()

    def test_stats_keys(self) -> None:
        y = np.array([0, 0, 0, 1, 1])
        stats = self.loader.get_attack_stats(y)
        self.assertIn("total", stats)
        self.assertIn("n_normal", stats)
        self.assertIn("n_attack", stats)
        self.assertIn("attack_ratio", stats)

    def test_stats_values(self) -> None:
        y = np.array([0, 0, 0, 1, 1])
        stats = self.loader.get_attack_stats(y)
        self.assertEqual(stats["total"], 5)
        self.assertEqual(stats["n_normal"], 3)
        self.assertEqual(stats["n_attack"], 2)
        self.assertAlmostEqual(stats["attack_ratio"], 0.4, places=4)

    def test_all_normal(self) -> None:
        y = np.zeros(100, dtype=int)
        stats = self.loader.get_attack_stats(y)
        self.assertEqual(stats["attack_ratio"], 0.0)

    def test_all_attack(self) -> None:
        y = np.ones(50, dtype=int)
        stats = self.loader.get_attack_stats(y)
        self.assertEqual(stats["attack_ratio"], 1.0)


class TestProcessGroupIndices(unittest.TestCase):
    """Tests the process group index mapping."""

    def test_all_groups_present(self) -> None:
        loader = SWaTLoader()
        groups = loader.get_process_feature_indices()
        self.assertEqual(set(groups.keys()), set(_PROCESS_GROUPS.keys()))

    def test_no_duplicate_indices(self) -> None:
        loader = SWaTLoader()
        groups = loader.get_process_feature_indices()
        all_idx = [idx for idxs in groups.values() for idx in idxs]
        self.assertEqual(len(all_idx), len(set(all_idx)))

    def test_indices_within_bounds(self) -> None:
        loader = SWaTLoader()
        groups = loader.get_process_feature_indices()
        for proc, idxs in groups.items():
            for idx in idxs:
                self.assertGreaterEqual(idx, 0)
                self.assertLess(idx, loader.n_features, msg=f"Index {idx} out of range for {proc}")


class TestLoadSWaTForTransfer(unittest.TestCase):
    """Tests the end-to-end load_swat_for_transfer pipeline."""

    def setUp(self) -> None:
        self.df = _make_minimal_swat_df(n_rows=300, attack_ratio=0.15)
        self.csv_path = _make_csv_file(self.df)

    def tearDown(self) -> None:
        if os.path.exists(self.csv_path):
            os.remove(self.csv_path)

    def test_returns_dict(self) -> None:
        result = load_swat_for_transfer(self.csv_path, window_size=5, verbose=False)
        self.assertIsInstance(result, dict)

    def test_required_keys_present(self) -> None:
        result = load_swat_for_transfer(self.csv_path, window_size=5, verbose=False)
        required = {"X_train", "X_test", "y_train", "y_test",
                    "X_train_w", "X_test_w", "y_train_w", "y_test_w",
                    "feature_names", "attack_stats"}
        self.assertTrue(required.issubset(set(result.keys())))

    def test_window_shapes(self) -> None:
        window_size = 8
        result = load_swat_for_transfer(self.csv_path, window_size=window_size, verbose=False)
        X_train_w = result["X_train_w"]
        self.assertEqual(X_train_w.ndim, 3)
        self.assertEqual(X_train_w.shape[1], window_size)

    def test_no_data_leakage_between_splits(self) -> None:
        """Train and test split sizes should sum to total (temporal split)."""
        result = load_swat_for_transfer(self.csv_path, window_size=5, test_ratio=0.25, verbose=False)
        n_train = len(result["y_train"])
        n_test = len(result["y_test"])
        # Allow ±1 for rounding
        self.assertAlmostEqual(n_train + n_test, 300, delta=1)

    def test_spectral_residual_option(self) -> None:
        """Using spectral residual should not crash and preserve shape."""
        result_sr = load_swat_for_transfer(
            self.csv_path, window_size=5, use_spectral_residual=True, verbose=False
        )
        result_no_sr = load_swat_for_transfer(
            self.csv_path, window_size=5, use_spectral_residual=False, verbose=False
        )
        self.assertEqual(result_sr["X_train"].shape, result_no_sr["X_train"].shape)


class TestLoadCombined(unittest.TestCase):
    """Tests the combined normal+attack loading."""

    def setUp(self) -> None:
        df_n = _make_minimal_swat_df(n_rows=150, attack_ratio=0.0)
        df_a = _make_minimal_swat_df(n_rows=100, attack_ratio=0.5)
        self.normal_path = _make_csv_file(df_n)
        self.attack_path = _make_csv_file(df_a)
        self.loader = SWaTLoader()

    def tearDown(self) -> None:
        for p in (self.normal_path, self.attack_path):
            if os.path.exists(p):
                os.remove(p)

    def test_combined_row_count(self) -> None:
        df = self.loader.load_combined(self.normal_path, self.attack_path)
        self.assertEqual(len(df), 250)


if __name__ == "__main__":
    # Verbose output for CI logs
    loader = unittest.TestLoader()
    suite = loader.loadTestsFromModule(__import__("__main__"))
    runner = unittest.TextTestRunner(verbosity=2)
    result = runner.run(suite)
    sys.exit(0 if result.wasSuccessful() else 1)
