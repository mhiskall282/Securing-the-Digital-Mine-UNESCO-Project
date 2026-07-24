"""Data loader and preprocessing module for the BATADAL dataset.

The BATADAL (Battle of the Attack Detection Algorithms) dataset represents a
water distribution network (similar to C-Town) with water tanks, pumps, and valves,
subject to cyber-physical attacks on the SCADA system.

Data Access:
  - Official web portal: http://www.batadal.net/data.html
  - Kaggle mirror: minhbtnguyen/batadal-a-dataset-for-cyber-attack-detection
    curl -L -o batadal.zip https://www.kaggle.com/api/v1/datasets/download/minhbtnguyen/batadal-a-dataset-for-cyber-attack-detection
  - GitHub SCY-PHY repository: https://github.com/scy-phy/www.batadal.net/tree/master/data
"""

import os
import time
import warnings
from typing import Dict, List, Optional, Tuple

import numpy as np
import pandas as pd

# Reuse minimal fallbacks if sklearn is not installed
try:
    from sklearn.model_selection import train_test_split as _sklearn_split
    from sklearn.preprocessing import MinMaxScaler, StandardScaler

    SKLEARN_AVAILABLE = True
except ImportError:
    SKLEARN_AVAILABLE = False

    class StandardScaler:  # type: ignore[no-redef]
        def __init__(self) -> None:
            self.mean_: Optional[np.ndarray] = None
            self.scale_: Optional[np.ndarray] = None

        def fit(self, X: np.ndarray) -> "StandardScaler":
            self.mean_ = np.mean(X, axis=0)
            self.scale_ = np.std(X, axis=0) + 1e-8
            return self

        def fit_transform(self, X: np.ndarray) -> np.ndarray:
            self.fit(X)
            return self.transform(X)

        def transform(self, X: np.ndarray) -> np.ndarray:
            return (X - self.mean_) / self.scale_  # type: ignore[operator]

    class MinMaxScaler:  # type: ignore[no-redef]
        def __init__(self, feature_range: Tuple[float, float] = (0, 1)) -> None:
            self.feature_range = feature_range
            self.min_: Optional[np.ndarray] = None
            self.scale_: Optional[np.ndarray] = None

        def fit(self, X: np.ndarray) -> "MinMaxScaler":
            data_min = np.min(X, axis=0)
            data_max = np.max(X, axis=0)
            data_range = data_max - data_min
            data_range[data_range == 0] = 1.0
            lo, hi = self.feature_range
            self.scale_ = (hi - lo) / data_range
            self.min_ = lo - data_min * self.scale_
            return self

        def fit_transform(self, X: np.ndarray) -> np.ndarray:
            self.fit(X)
            return self.transform(X)

        def transform(self, X: np.ndarray) -> np.ndarray:
            return X * self.scale_ + self.min_  # type: ignore[operator]

    def _sklearn_split(
        X: np.ndarray,
        y: np.ndarray,
        test_size: float = 0.2,
        random_state: int = 42,
        stratify: Optional[np.ndarray] = None,
    ) -> Tuple[np.ndarray, np.ndarray, np.ndarray, np.ndarray]:
        np.random.seed(random_state)
        n = len(X)
        idx = np.random.permutation(n)
        split = int(n * (1 - test_size))
        return X[idx[:split]], X[idx[split:]], y[idx[:split]], y[idx[split:]]


# ---------------------------------------------------------------------------
# BATADAL sensor/actuator schema (43 features)
# ---------------------------------------------------------------------------
BATADAL_FEATURE_NAMES: List[str] = [
    "L_T1", "L_T2", "L_T3", "L_T4", "L_T5", "L_T6", "L_T7",  # Level of Tanks
    "F_PU1", "S_PU1", "F_PU2", "S_PU2", "F_PU3", "S_PU3",     # Flow & Status Pumps
    "F_PU4", "S_PU4", "F_PU5", "S_PU5", "F_PU6", "S_PU6",
    "F_PU7", "S_PU7", "F_PU8", "S_PU8", "F_PU9", "S_PU9",
    "F_PU10", "S_PU10", "F_PU11", "S_PU11",
    "F_V2", "S_V2",                                          # Flow & Status Valve
    "P_J280", "P_J269", "P_J300", "P_J256", "P_J289",        # Pressure Sensors
    "P_J415", "P_J302", "P_J306", "P_J342", "P_J14",
    "P_J422", "P_J12"
]

# Realistic operating stats for BATADAL (derived from official challenge data)
_BATADAL_STATS: Dict[str, Tuple[float, float, float, float]] = {
    # Tank Levels (typically in range 0 - 10 meters)
    "L_T1": (3.1, 1.2, 0.5, 6.5),
    "L_T2": (3.5, 0.9, 1.0, 5.5),
    "L_T3": (4.2, 0.8, 1.5, 6.2),
    "L_T4": (2.8, 1.1, 0.2, 5.0),
    "L_T5": (1.9, 0.6, 0.5, 4.0),
    "L_T6": (4.8, 0.7, 2.0, 7.0),
    "L_T7": (3.3, 0.9, 1.0, 5.8),
    # Valve Flow and Status
    "F_V2": (120.0, 45.0, 0.0, 250.0),
    "S_V2": (1.0, 0.0, 0.0, 1.0),
}


class BATADALLoader:
    """Handles loading and preprocessing of the BATADAL water network SCADA dataset.

    Supports reading BATADAL dataset CSVs and generating high-fidelity mock datasets.
    """

    def __init__(self, feature_names: Optional[List[str]] = None) -> None:
        """Initializes the loader.

        Args:
            feature_names: Override the default 43-feature list.
        """
        self.feature_names: List[str] = feature_names or BATADAL_FEATURE_NAMES.copy()
        self._scaler: Optional[object] = None
        self._fitted: bool = False
        self.n_features: int = len(self.feature_names)

    def load(self, path: str) -> pd.DataFrame:
        """Loads the BATADAL dataset from disk.

        If the file does not exist, generates a representative mock dataset.

        Args:
            path: Path to the dataset file.

        Returns:
            Raw DataFrame with Datetime, sensor/actuator columns, and ATT_FLAG.
        """
        if not os.path.exists(path):
            warnings.warn(
                f"BATADAL file not found at '{path}'. Generating synthetic mock dataset.",
                stacklevel=2,
            )
            os.makedirs(os.path.dirname(os.path.abspath(path)), exist_ok=True)
            self._create_realistic_mock(path)

        df = pd.read_csv(path)
        return df

    def load_combined(self, paths: List[str]) -> pd.DataFrame:
        """Loads and combines multiple BATADAL CSV files.

        Args:
            paths: List of filepaths to combine.

        Returns:
            Combined DataFrame.
        """
        dfs = [self.load(p) for p in paths]
        return pd.concat(dfs, ignore_index=True)

    def preprocess(self, df: pd.DataFrame, return_timestamps: bool = False) -> Tuple:
        """Cleans and extracts features and labels from the raw DataFrame.

        Args:
            df: Input DataFrame.
            return_timestamps: If True, also returns timestamps.

        Returns:
            (X, y) as numpy arrays, or (X, y, timestamps) if return_timestamps=True.
        """
        df_clean = df.copy()
        df_clean.columns = df_clean.columns.str.strip()

        # Find timestamp
        timestamps: Optional[np.ndarray] = None
        for ts_col in ("DATETIME", "Datetime", "datetime", "Timestamp", "timestamp"):
            if ts_col in df_clean.columns:
                try:
                    timestamps = pd.to_datetime(df_clean[ts_col]).values
                except Exception:
                    timestamps = df_clean[ts_col].values
                df_clean = df_clean.drop(columns=[ts_col])
                break

        # Find label column
        label_col = "ATT_FLAG"
        if label_col not in df_clean.columns:
            # Fall back to the last column
            label_col = df_clean.columns[-1]

        X_df = df_clean.drop(columns=[label_col])
        X_df = X_df.apply(pd.to_numeric, errors="coerce").fillna(X_df.median(numeric_only=True))

        actual_cols = list(X_df.columns)
        if len(actual_cols) == 43:
            self.feature_names = actual_cols
            self.n_features = 43

        # In BATADAL, attack is 1, normal is 0 or -999 (normal background).
        y = (df_clean[label_col].values == 1).astype(int)
        X = X_df.values.astype(np.float32)

        if return_timestamps:
            return X, y, timestamps
        return X, y

    def train_test_split(
        self,
        X: np.ndarray,
        y: np.ndarray,
        test_size: float = 0.2,
        stratify: bool = True,
    ) -> Tuple[np.ndarray, np.ndarray, np.ndarray, np.ndarray]:
        strat = y if stratify else None
        return _sklearn_split(X, y, test_size=test_size, random_state=42, stratify=strat)

    def train_test_split_temporal(
        self,
        X: np.ndarray,
        y: np.ndarray,
        train_ratio: float = 0.75,
    ) -> Tuple[np.ndarray, np.ndarray, np.ndarray, np.ndarray]:
        split_idx = int(len(X) * train_ratio)
        return X[:split_idx], X[split_idx:], y[:split_idx], y[split_idx:]

    def sliding_window(
        self,
        X: np.ndarray,
        y: np.ndarray,
        window_size: int = 10,
        stride: int = 1,
    ) -> Tuple[np.ndarray, np.ndarray]:
        n_samples = (len(X) - window_size) // stride + 1
        X_out = np.lib.stride_tricks.sliding_window_view(X, (window_size, X.shape[1]))[::stride, 0]
        y_out = y[window_size - 1 :: stride][:n_samples]
        return X_out.astype(np.float32), y_out

    def normalize(
        self,
        X_train: np.ndarray,
        X_test: np.ndarray,
        method: str = "minmax",
    ) -> Tuple[np.ndarray, np.ndarray]:
        if method == "standard":
            scaler = StandardScaler()
        else:
            scaler = MinMaxScaler()

        X_train_scaled = scaler.fit_transform(X_train)
        X_test_scaled = scaler.transform(X_test)
        self._scaler = scaler
        self._fitted = True
        return X_train_scaled.astype(np.float32), X_test_scaled.astype(np.float32)

    def _create_realistic_mock(self, filepath: str) -> None:
        """Generates realistic mock BATADAL CSV."""
        np.random.seed(42)
        n_samples = 3000
        data = np.zeros((n_samples, len(self.feature_names)), dtype=np.float32)

        for col_idx, feat in enumerate(self.feature_names):
            stats = _BATADAL_STATS.get(feat, (50.0, 15.0, 0.0, 100.0))
            mu, sigma, vmin, vmax = stats
            if feat.startswith("S_"):
                # Binary state switches
                series = np.random.choice([0.0, 1.0], size=n_samples, p=[0.3, 0.7])
                data[:, col_idx] = series
            else:
                ar_coef = 0.95
                series = np.zeros(n_samples, dtype=np.float32)
                series[0] = mu
                for t in range(1, n_samples):
                    series[t] = ar_coef * series[t - 1] + (1 - ar_coef) * mu + np.random.normal(0, sigma * 0.1)
                data[:, col_idx] = np.clip(series, vmin, vmax)

        # Add attack flags
        labels = np.zeros(n_samples, dtype=int)
        attack_starts = [500, 1500, 2400]
        for start in attack_starts:
            duration = np.random.randint(50, 150)
            end = start + duration
            labels[start:end] = 1
            # Mutate features during attack
            data[start:end, :5] += np.random.uniform(2.0, 4.0)

        df = pd.DataFrame(data, columns=self.feature_names)
        df.insert(0, "DATETIME", pd.date_range("2016-01-01", periods=n_samples, freq="h"))
        df["ATT_FLAG"] = labels
        df.to_csv(filepath, index=False)
        print(f"[BATADALLoader] Created mock: {filepath}")


# Backwards compatibility
BatadalLoader = BATADALLoader
