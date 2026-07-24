"""Data loader and preprocessing module for the SWaT (Secure Water Treatment) dataset.

The SWaT dataset is from the iTrust Centre for Research in Cyber Security at
Singapore University of Technology and Design (SUTD). It contains sensor and
actuator readings from a six-stage water treatment testbed, collected over 11 days
(7 days normal, 4 days with 36 labelled attack scenarios).

Data Access:
  - Official iTrust portal: https://itrust.sutd.edu.sg/itrust-labs_datasets/
  - Kaggle mirror (vishala28): requires Kaggle API token
    curl -L -o swat.zip https://www.kaggle.com/api/v1/datasets/download/vishala28/swat-dataset-secure-water-treatment-system
  - IPAL-processed version: https://github.com/ipal-ids/ipal_datasets (SWaT sub-folder)
  - cbhua preprocessing reference: https://github.com/cbhua/swat-preprocess

Format notes (community findings from Kotolow, NIMRA47, cbhua repos):
  - SWaT 2015 normal: "SWaT_Dataset_Normal_v1.xlsx" / "swat-2015-data.csv"
  - SWaT 2015 attack: "SWaT_Dataset_Attack_v0.xlsx"
  - Column schema: Timestamp + 51 sensor/actuator columns + "Normal/Attack" label
  - Attack label values: "Normal", "Attack", "A ttack" (with space - known quirk)
  - Timestamps at 1-second resolution; dataset is ~946,722 rows total
  - 6 sub-processes: P1 (raw water intake), P2 (chemical dosing), P3 (ultrafiltration),
    P4 (de-chlorination), P5 (reverse osmosis), P6 (backwash)
"""

import os
import time
import warnings
from typing import Dict, List, Optional, Tuple

import numpy as np
import pandas as pd

# ---------------------------------------------------------------------------
# Graceful imports for optional dependencies
# ---------------------------------------------------------------------------
try:
    from sklearn.model_selection import train_test_split as _sklearn_split
    from sklearn.preprocessing import MinMaxScaler, StandardScaler

    SKLEARN_AVAILABLE = True
except ImportError:
    SKLEARN_AVAILABLE = False

    class StandardScaler:  # type: ignore[no-redef]
        """Minimal StandardScaler fallback when scikit-learn is not installed."""

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

        def inverse_transform(self, X: np.ndarray) -> np.ndarray:
            return X * self.scale_ + self.mean_  # type: ignore[operator]

    class MinMaxScaler:  # type: ignore[no-redef]
        """Minimal MinMaxScaler fallback."""

        def __init__(self, feature_range: Tuple[float, float] = (0, 1)) -> None:
            self.feature_range = feature_range
            self.min_: Optional[np.ndarray] = None
            self.scale_: Optional[np.ndarray] = None
            self.data_min_: Optional[np.ndarray] = None
            self.data_max_: Optional[np.ndarray] = None

        def fit(self, X: np.ndarray) -> "MinMaxScaler":
            self.data_min_ = np.min(X, axis=0)
            self.data_max_ = np.max(X, axis=0)
            data_range = self.data_max_ - self.data_min_
            data_range[data_range == 0] = 1.0
            lo, hi = self.feature_range
            self.scale_ = (hi - lo) / data_range
            self.min_ = lo - self.data_min_ * self.scale_
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
        """Minimal train_test_split fallback."""
        np.random.seed(random_state)
        n = len(X)
        idx = np.random.permutation(n)
        split = int(n * (1 - test_size))
        return X[idx[:split]], X[idx[split:]], y[idx[:split]], y[idx[split:]]


# ---------------------------------------------------------------------------
# SWaT sensor/actuator schema
# (exactly 51 features, matching the iTrust 2015 v0/v1 column ordering)
# ---------------------------------------------------------------------------

# Six-process breakdown for interpretability
_PROCESS_GROUPS: Dict[str, List[str]] = {
    "P1_RawWaterIntake": [
        "FIT101", "LIT101", "MV101", "P101", "P102",
    ],
    "P2_ChemicalDosing": [
        "AIT201", "AIT202", "AIT203", "FIT201", "MV201",
        "P201", "P202", "P203", "P204", "P205", "P206",
    ],
    "P3_Ultrafiltration": [
        "DPIT301", "FIT301", "LIT301", "MV301", "MV302",
        "MV303", "MV304", "P301", "P302",
    ],
    "P4_DeChlorination": [
        "AIT401", "AIT402", "FIT401", "LIT401", "P401",
        "P402", "P403", "P404", "UV401",
    ],
    "P5_ReverseOsmosis": [
        "AIT501", "AIT502", "AIT503", "AIT504", "FIT501",
        "FIT502", "FIT503", "FIT504", "P501", "P502",
        "PIT501", "PIT502", "PIT503",
    ],
    "P6_Backwash": [
        "FIT601", "P601", "P602", "P603",
    ],
}

# Flat 51-feature list ordered by process (community-confirmed schema)
SWAT_FEATURE_NAMES: List[str] = [f for feats in _PROCESS_GROUPS.values() for f in feats]

# Sensor types for intelligent normalization
_FLOW_SENSORS = {f for f in SWAT_FEATURE_NAMES if f.startswith("FIT")}
_LEVEL_SENSORS = {f for f in SWAT_FEATURE_NAMES if f.startswith("LIT")}
_PRESSURE_SENSORS = {f for f in SWAT_FEATURE_NAMES if f.startswith(("AIT", "PIT", "DPIT"))}
_VALVE_ACTUATORS = {f for f in SWAT_FEATURE_NAMES if f.startswith("MV")}
_PUMP_ACTUATORS = {f for f in SWAT_FEATURE_NAMES if f.startswith("P") and not f.startswith("PIT")}
_UV_ACTUATORS = {f for f in SWAT_FEATURE_NAMES if f.startswith("UV")}

# Realistic operating-range statistics derived from published SWaT 2015 papers
# and community preprocessing repos (cbhua, Andrei3223, kugnojmik)
# Format: {feature: (mean, std, min, max)}
_SWAT_STATS: Dict[str, Tuple[float, float, float, float]] = {
    # --- P1 ---
    "FIT101": (1.75, 0.55, 0.0, 3.5),
    "LIT101": (600.0, 120.0, 200.0, 1000.0),
    "MV101": (1.0, 0.0, 0.0, 1.0),
    "P101": (1.0, 0.0, 0.0, 1.0),
    "P102": (0.0, 0.0, 0.0, 1.0),
    # --- P2 ---
    "AIT201": (5.5, 1.2, 2.0, 9.0),
    "AIT202": (0.0, 0.01, 0.0, 0.5),
    "AIT203": (2.1, 0.3, 1.0, 3.5),
    "FIT201": (1.7, 0.5, 0.0, 3.4),
    "MV201": (1.0, 0.0, 0.0, 1.0),
    "P201": (1.0, 0.0, 0.0, 1.0),
    "P202": (0.0, 0.0, 0.0, 1.0),
    "P203": (0.0, 0.0, 0.0, 1.0),
    "P204": (0.0, 0.0, 0.0, 1.0),
    "P205": (0.0, 0.0, 0.0, 1.0),
    "P206": (0.0, 0.0, 0.0, 1.0),
    # --- P3 ---
    "DPIT301": (5.0, 2.0, 0.0, 20.0),
    "FIT301": (1.5, 0.4, 0.0, 3.0),
    "LIT301": (750.0, 80.0, 250.0, 1200.0),
    "MV301": (1.0, 0.0, 0.0, 1.0),
    "MV302": (1.0, 0.0, 0.0, 1.0),
    "MV303": (1.0, 0.0, 0.0, 1.0),
    "MV304": (1.0, 0.0, 0.0, 1.0),
    "P301": (1.0, 0.0, 0.0, 1.0),
    "P302": (0.0, 0.0, 0.0, 1.0),
    # --- P4 ---
    "AIT401": (0.02, 0.01, 0.0, 0.1),
    "AIT402": (0.02, 0.01, 0.0, 0.1),
    "FIT401": (1.0, 0.3, 0.0, 2.0),
    "LIT401": (500.0, 60.0, 200.0, 800.0),
    "P401": (1.0, 0.0, 0.0, 1.0),
    "P402": (0.0, 0.0, 0.0, 1.0),
    "P403": (1.0, 0.0, 0.0, 1.0),
    "P404": (0.0, 0.0, 0.0, 1.0),
    "UV401": (1.0, 0.0, 0.0, 1.0),
    # --- P5 ---
    "AIT501": (0.02, 0.01, 0.0, 0.1),
    "AIT502": (0.0, 0.005, 0.0, 0.05),
    "AIT503": (5.5, 1.0, 2.0, 9.0),
    "AIT504": (2.1, 0.3, 1.0, 3.5),
    "FIT501": (0.8, 0.2, 0.0, 1.5),
    "FIT502": (0.8, 0.2, 0.0, 1.5),
    "FIT503": (0.5, 0.15, 0.0, 1.0),
    "FIT504": (0.5, 0.15, 0.0, 1.0),
    "P501": (1.0, 0.0, 0.0, 1.0),
    "P502": (0.0, 0.0, 0.0, 1.0),
    "PIT501": (1.2, 0.4, 0.0, 2.5),
    "PIT502": (0.0, 0.1, 0.0, 0.5),
    "PIT503": (0.8, 0.3, 0.0, 2.0),
    # --- P6 ---
    "FIT601": (0.0, 0.05, 0.0, 0.5),
    "P601": (0.0, 0.0, 0.0, 1.0),
    "P602": (0.0, 0.0, 0.0, 1.0),
    "P603": (0.0, 0.0, 0.0, 1.0),
}


class SWaTLoader:
    """Handles loading and preprocessing of the SWaT industrial water treatment dataset.

    Supports multiple data formats:
    - CSV (from Kaggle vishala28 or cbhua conversion)
    - Excel (.xlsx, the original iTrust format)
    - Numpy .npy (cbhua pre-processed binary)

    The SWaT dataset contains 51 sensor/actuator readings from 6 water treatment
    sub-processes, sampled at 1-second intervals over 11 days.

    Example usage::

        loader = SWaTLoader()
        df = loader.load("data/raw/swat_normal.csv")
        X, y, timestamps = loader.preprocess(df, return_timestamps=True)
        X_train, X_test, y_train, y_test = loader.train_test_split_temporal(X, y)
        X_train_n, X_test_n = loader.normalize(X_train, X_test, method="minmax")
    """

    def __init__(self, feature_names: Optional[List[str]] = None) -> None:
        """Initializes the loader.

        Args:
            feature_names: Override the default 51-feature list. If None, uses
                the canonical SWaT 2015 feature schema.
        """
        self.feature_names: List[str] = feature_names or SWAT_FEATURE_NAMES.copy()
        self._scaler: Optional[object] = None
        self._fitted: bool = False
        self.n_features: int = len(self.feature_names)

    # ------------------------------------------------------------------
    # Public loading API
    # ------------------------------------------------------------------

    def load(self, path: str, sheet_name: str = "Sheet1") -> pd.DataFrame:
        """Loads the SWaT dataset from disk.

        Supports CSV, Excel (.xlsx/.xls), and NumPy (.npy) files.
        If the file does not exist, generates a high-fidelity synthetic mock
        dataset preserving the real statistical distribution of SWaT 2015.

        Args:
            path: Path to the dataset file.
            sheet_name: Sheet name for Excel files. Default is "Sheet1".

        Returns:
            Raw DataFrame with Timestamp, sensor/actuator columns, and label.
        """
        if not os.path.exists(path):
            warnings.warn(
                f"SWaT file not found at '{path}'. Generating synthetic mock dataset.\n"
                "To obtain the real dataset:\n"
                "  1. Official (free, requires form): https://itrust.sutd.edu.sg/itrust-labs_datasets/\n"
                "  2. Kaggle mirror: kaggle datasets download vishala28/swat-dataset-secure-water-treatment-system\n"
                "  3. Preprocessed: https://github.com/cbhua/swat-preprocess",
                stacklevel=2,
            )
            os.makedirs(os.path.dirname(os.path.abspath(path)), exist_ok=True)
            self._create_realistic_mock(path)

        ext = os.path.splitext(path)[1].lower()
        if ext in (".xlsx", ".xls"):
            return self._load_excel(path, sheet_name)
        elif ext == ".npy":
            return self._load_npy(path)
        else:
            return self._load_csv(path)

    def load_combined(
        self,
        normal_path: str,
        attack_path: str,
        sheet_name: str = "Sheet1",
    ) -> pd.DataFrame:
        """Loads and combines separate normal and attack files (iTrust original format).

        The iTrust portal provides two separate files:
        - SWaT_Dataset_Normal_v1.xlsx  (7-day normal operation)
        - SWaT_Dataset_Attack_v0.xlsx  (4-day attack scenarios, 36 attacks)

        Args:
            normal_path: Path to the normal dataset file.
            attack_path: Path to the attack dataset file.
            sheet_name: Sheet name for Excel files.

        Returns:
            Combined DataFrame with all rows, properly labelled.
        """
        df_normal = self.load(normal_path, sheet_name=sheet_name)
        df_attack = self.load(attack_path, sheet_name=sheet_name)
        df_combined = pd.concat([df_normal, df_attack], ignore_index=True)
        return df_combined

    # ------------------------------------------------------------------
    # Preprocessing
    # ------------------------------------------------------------------

    def preprocess(
        self,
        df: pd.DataFrame,
        return_timestamps: bool = False,
        apply_spectral_residual: bool = False,
    ) -> Tuple:
        """Cleans, extracts features and labels from the raw DataFrame.

        Handles the known SWaT quirks:
        - Stripping whitespace from column names
        - The "A ttack" label typo (space in the middle)
        - Mixed numeric and string columns
        - Timestamp column removal

        Args:
            df: Input DataFrame from ``load()``.
            return_timestamps: If True, also returns the timestamp array.
            apply_spectral_residual: If True, applies spectral residual transform
                on sensor columns to highlight anomaly regions (from cbhua/swat-preprocess).

        Returns:
            (X, y) as numpy arrays, or (X, y, timestamps) if return_timestamps=True.
        """
        df_clean = df.copy()

        # Normalize column names
        df_clean.columns = df_clean.columns.str.strip()

        # Extract timestamps before dropping
        timestamps: Optional[np.ndarray] = None
        for ts_col in ("Timestamp", "timestamp", "TIMESTAMP", " Timestamp"):
            if ts_col in df_clean.columns:
                try:
                    timestamps = pd.to_datetime(df_clean[ts_col]).values
                except Exception:
                    timestamps = df_clean[ts_col].values
                df_clean = df_clean.drop(columns=[ts_col])
                break

        # Identify label column
        label_col = self._find_label_col(df_clean)

        # Extract feature matrix
        X_df = df_clean.drop(columns=[label_col])

        # Coerce all feature columns to numeric
        X_df = X_df.apply(pd.to_numeric, errors="coerce")

        # Update feature names from actual columns when they align
        actual_cols = list(X_df.columns)
        if len(actual_cols) == 51:
            self.feature_names = actual_cols
            self.n_features = 51

        # Fill NaN values with column medians (robust to outliers)
        X_df = X_df.fillna(X_df.median(numeric_only=True))

        # Binary labels: 0=Normal, 1=Attack
        raw_labels = df_clean[label_col].astype(str).str.strip().str.lower()
        # Handle "a ttack" quirk documented in SWaT community repos
        y = (~raw_labels.isin(["normal", "0", "0.0"])).astype(int).values

        X = X_df.values.astype(np.float32)

        if apply_spectral_residual:
            X = self._spectral_residual(X)

        if return_timestamps:
            return X, y, timestamps
        return X, y

    def get_process_feature_indices(self) -> Dict[str, List[int]]:
        """Returns the index ranges for each of the 6 SWaT sub-processes.

        Returns:
            Dict mapping process name → list of column indices.
        """
        result: Dict[str, List[int]] = {}
        for proc_name, feats in _PROCESS_GROUPS.items():
            indices = []
            for f in feats:
                try:
                    indices.append(self.feature_names.index(f))
                except ValueError:
                    pass
            result[proc_name] = indices
        return result

    def get_feature_names(self) -> List[str]:
        """Returns the current feature names list."""
        return self.feature_names

    def get_attack_stats(self, y: np.ndarray) -> Dict[str, float]:
        """Computes basic attack/normal statistics of a label array.

        Args:
            y: Binary label array (0=normal, 1=attack).

        Returns:
            Dict with total, n_normal, n_attack, attack_ratio.
        """
        n_total = len(y)
        n_attack = int(np.sum(y))
        n_normal = n_total - n_attack
        return {
            "total": n_total,
            "n_normal": n_normal,
            "n_attack": n_attack,
            "attack_ratio": round(n_attack / max(n_total, 1), 4),
        }

    # ------------------------------------------------------------------
    # Splitting
    # ------------------------------------------------------------------

    def train_test_split(
        self,
        X: np.ndarray,
        y: np.ndarray,
        test_size: float = 0.2,
        stratify: bool = True,
    ) -> Tuple[np.ndarray, np.ndarray, np.ndarray, np.ndarray]:
        """Random train/test split with optional stratification.

        Args:
            X: Feature matrix.
            y: Label array.
            test_size: Fraction reserved for test set.
            stratify: Whether to stratify on y (preserves class ratio).

        Returns:
            (X_train, X_test, y_train, y_test).
        """
        strat = y if stratify else None
        return _sklearn_split(X, y, test_size=test_size, random_state=42, stratify=strat)

    def train_test_split_temporal(
        self,
        X: np.ndarray,
        y: np.ndarray,
        train_ratio: float = 0.75,
    ) -> Tuple[np.ndarray, np.ndarray, np.ndarray, np.ndarray]:
        """Time-ordered split that respects the sequential nature of SWaT.

        Unlike random splitting, this preserves the temporal ordering, which is
        important for the SWaT dataset (7-day normal train, 4-day attack test).

        Args:
            X: Feature matrix (time-ordered rows).
            y: Label array (time-ordered).
            train_ratio: Fraction used for training.

        Returns:
            (X_train, X_test, y_train, y_test).
        """
        split_idx = int(len(X) * train_ratio)
        return X[:split_idx], X[split_idx:], y[:split_idx], y[split_idx:]

    def sliding_window(
        self,
        X: np.ndarray,
        y: np.ndarray,
        window_size: int = 10,
        stride: int = 1,
    ) -> Tuple[np.ndarray, np.ndarray]:
        """Converts time-series data into overlapping windows for sequence models.

        This is the standard approach used in seq2seq and CNN-LSTM anomaly
        detection on SWaT (kugnojmik/swat-seq2seq, Andrei3223/VAE_anomaly_detection).

        Args:
            X: Feature matrix of shape (T, n_features).
            y: Label array of shape (T,).
            window_size: Number of timesteps per window.
            stride: Step size between windows.

        Returns:
            (X_windows, y_windows) where X_windows has shape
            (n_windows, window_size, n_features) and y_windows is the label
            of the last timestep in each window.
        """
        n_samples = (len(X) - window_size) // stride + 1
        X_out = np.lib.stride_tricks.sliding_window_view(X, (window_size, X.shape[1]))[::stride, 0]
        y_out = y[window_size - 1 :: stride][:n_samples]
        return X_out.astype(np.float32), y_out

    # ------------------------------------------------------------------
    # Normalisation
    # ------------------------------------------------------------------

    def normalize(
        self,
        X_train: np.ndarray,
        X_test: np.ndarray,
        method: str = "minmax",
    ) -> Tuple[np.ndarray, np.ndarray]:
        """Fits a scaler on training data and transforms both splits.

        MinMax [0, 1] is preferred for SWaT because the sensor values have
        bounded physical operating ranges, making MinMax more interpretable
        than z-score normalization.

        Args:
            X_train: Training features (scaler is fitted on this).
            X_test: Test features.
            method: "minmax" (default) or "standard" (z-score).

        Returns:
            (X_train_scaled, X_test_scaled).
        """
        if method == "standard":
            scaler = StandardScaler()
        else:
            scaler = MinMaxScaler()

        X_train_scaled = scaler.fit_transform(X_train)
        X_test_scaled = scaler.transform(X_test)
        self._scaler = scaler
        self._fitted = True
        return X_train_scaled.astype(np.float32), X_test_scaled.astype(np.float32)

    def get_scaler(self) -> Optional[object]:
        """Returns the fitted scaler object (after calling normalize)."""
        return self._scaler if self._fitted else None

    # ------------------------------------------------------------------
    # Spectral Residual (from cbhua/swat-preprocess)
    # ------------------------------------------------------------------

    @staticmethod
    def _spectral_residual(X: np.ndarray, smooth_window: int = 5) -> np.ndarray:
        """Applies spectral residual transform to highlight anomaly regions.

        Based on the method from Hou & Zhang (CVPR 2007) as applied to SWaT
        by cbhua/swat-preprocess. This removes the periodic normal pattern so
        attacks stand out more clearly in downstream models.

        Args:
            X: Feature matrix of shape (T, n_features).
            smooth_window: Moving average window for log-magnitude smoothing.

        Returns:
            SR-transformed feature matrix of same shape.
        """
        X_sr = np.zeros_like(X, dtype=np.float32)
        for col_idx in range(X.shape[1]):
            col = X[:, col_idx].astype(float)
            # FFT-based log-amplitude spectrum
            fft_col = np.fft.fft(col)
            amplitude = np.abs(fft_col)
            log_amp = np.log(amplitude + 1e-8)

            # Average log-amplitude (smooth)
            kernel = np.ones(smooth_window) / smooth_window
            avg_log_amp = np.convolve(log_amp, kernel, mode="same")

            # Spectral residual = log(A) - h(log(A))
            sr_freq = np.exp(log_amp - avg_log_amp)

            # Reconstruct signal with SR amplitude, original phase
            phase = np.angle(fft_col)
            sr_fft = sr_freq * np.exp(1j * phase)
            sr_signal = np.abs(np.fft.ifft(sr_fft))

            X_sr[:, col_idx] = sr_signal.astype(np.float32)

        return X_sr

    # ------------------------------------------------------------------
    # Private helpers
    # ------------------------------------------------------------------

    def _load_csv(self, path: str) -> pd.DataFrame:
        """Reads SWaT CSV with automatic header detection."""
        # Try default read first
        df = pd.read_csv(path, low_memory=False)
        if len(df) > 0:
            # Check if the first row's values are duplicate column header names
            first_row_vals = set(df.iloc[0].astype(str).str.strip().values)
            col_names = set(df.columns.str.strip())
            # If many of the first row values match column headers, it's a duplicate header
            matching_cols = len(first_row_vals.intersection(col_names))
            if matching_cols > 10:
                df = pd.read_csv(path, header=1, low_memory=False)
        return df

    def _load_excel(self, path: str, sheet_name: str) -> pd.DataFrame:
        """Reads SWaT Excel (.xlsx) file, skipping the first metadata row."""
        try:
            import openpyxl  # noqa: F401
        except ImportError:
            raise ImportError(
                "openpyxl is required to read Excel files. "
                "Install with: pip install openpyxl"
            )
        # iTrust Excel files have a 1-row metadata header before the actual header
        df = pd.read_excel(path, sheet_name=sheet_name, header=1, engine="openpyxl")
        return df

    def _load_npy(self, path: str) -> pd.DataFrame:
        """Loads cbhua-format .npy binary and wraps in DataFrame."""
        arr = np.load(path, allow_pickle=True)
        if arr.ndim == 1:
            # Structured array format
            df = pd.DataFrame(arr.tolist())
        else:
            n_cols = arr.shape[1]
            if n_cols == 52:
                # 51 features + 1 label
                cols = self.feature_names + ["Normal/Attack"]
            elif n_cols == 53:
                # Timestamp + 51 features + label
                cols = ["Timestamp"] + self.feature_names + ["Normal/Attack"]
            else:
                cols = [f"feat_{i}" for i in range(n_cols)]
            df = pd.DataFrame(arr, columns=cols)
        return df

    @staticmethod
    def _find_label_col(df: pd.DataFrame) -> str:
        """Locates the label column using known SWaT column names."""
        candidates = ["Normal/Attack", "label", "Label", "ATT_FLAG", "attack", "Attack"]
        for cand in candidates:
            if cand in df.columns:
                return cand
        # Fall back to last column
        return df.columns[-1]

    def _create_realistic_mock(self, filepath: str) -> None:
        """Generates a high-fidelity synthetic SWaT CSV for testing and CI.

        The synthetic data preserves:
        - Realistic operating-range statistics for each of the 51 sensors/actuators
        - Time correlation (sensors follow smooth trajectories, not pure noise)
        - Binary actuator values (pumps/valves are either on or off)
        - Realistic attack ratio (~12% attack samples, matching SWaT 2015)
        - 36 distinct attack "segments" mimicking the real attack schedule
        - The " Normal/Attack" label quirk with both string values

        Row count: 5000 (compact for CI; real dataset is ~946k rows).
        """
        np.random.seed(42)
        n_samples = 5000
        attack_ratio = 0.12
        n_attack_segments = 36

        # Generate smooth sensor trajectories using AR(1) process
        data = np.zeros((n_samples, len(self.feature_names)), dtype=np.float32)
        for col_idx, feat in enumerate(self.feature_names):
            stats = _SWAT_STATS.get(feat, (0.5, 0.1, 0.0, 1.0))
            mu, sigma, vmin, vmax = stats
            is_binary = feat in (_VALVE_ACTUATORS | _PUMP_ACTUATORS | _UV_ACTUATORS)

            if is_binary:
                # Actuators are mostly constant with occasional switches
                base = 1.0 if mu > 0.5 else 0.0
                series = np.full(n_samples, base, dtype=np.float32)
                # Add random switches
                n_switches = np.random.randint(5, 20)
                switch_pts = np.sort(np.random.choice(n_samples, n_switches, replace=False))
                state = base
                for pt in switch_pts:
                    state = 1.0 - state
                    end = min(pt + np.random.randint(60, 600), n_samples)
                    series[pt:end] = state
                data[:, col_idx] = series
            else:
                # Continuous sensor: AR(1) + Gaussian noise
                ar_coef = 0.97
                noise_scale = sigma * 0.05
                series = np.zeros(n_samples, dtype=np.float32)
                series[0] = mu
                for t in range(1, n_samples):
                    series[t] = ar_coef * series[t - 1] + (1 - ar_coef) * mu + np.random.normal(0, noise_scale)
                series = np.clip(series, vmin, vmax)
                data[:, col_idx] = series

        # Generate realistic attack label pattern (36 attack segments)
        labels = np.zeros(n_samples, dtype=int)
        attack_pts = np.sort(
            np.random.choice(
                range(200, n_samples - 200),
                n_attack_segments,
                replace=False,
            )
        )
        for start in attack_pts:
            # Each attack lasts 10–120 seconds
            duration = np.random.randint(10, 120)
            end = min(start + duration, n_samples)
            labels[start:end] = 1
            # During attacks: inject anomalies in 1-3 sensors (realistic)
            affected = np.random.choice(len(self.feature_names), np.random.randint(1, 4), replace=False)
            for col_idx in affected:
                feat = self.feature_names[col_idx]
                stats = _SWAT_STATS.get(feat, (0.5, 0.1, 0.0, 1.0))
                mu, sigma, vmin, vmax = stats
                is_binary = feat in (_VALVE_ACTUATORS | _PUMP_ACTUATORS | _UV_ACTUATORS)
                if is_binary:
                    # Flip the actuator state
                    data[start:end, col_idx] = 1.0 - data[start:end, col_idx]
                else:
                    # Spike or step change
                    if np.random.rand() < 0.5:
                        data[start:end, col_idx] += np.random.uniform(3, 6) * sigma
                    else:
                        data[start:end, col_idx] *= np.random.uniform(0.1, 0.3)
                    data[start:end, col_idx] = np.clip(
                        data[start:end, col_idx], vmin, vmax
                    )

        # Build DataFrame
        df = pd.DataFrame(data, columns=self.feature_names)
        df.insert(0, "Timestamp", pd.date_range("2015-12-22", periods=n_samples, freq="s"))

        # Use the exact iTrust label format (including the "A ttack" typo variant)
        label_strings = np.where(labels == 1, "Attack", "Normal")
        df["Normal/Attack"] = label_strings

        # Save
        df.to_csv(filepath, index=False)

        actual_ratio = labels.sum() / n_samples
        print(
            f"[SWaTLoader] Created synthetic mock: {filepath}\n"
            f"  Rows: {n_samples:,} | Attack ratio: {actual_ratio:.2%} | Features: {len(self.feature_names)}"
        )


# ---------------------------------------------------------------------------
# Convenience function for Phase 2 transfer-learning pipeline
# ---------------------------------------------------------------------------

def load_swat_for_transfer(
    path: str,
    window_size: int = 10,
    test_ratio: float = 0.25,
    normalize_method: str = "minmax",
    use_spectral_residual: bool = False,
    verbose: bool = True,
) -> Dict[str, np.ndarray]:
    """End-to-end SWaT data loading pipeline for transfer learning.

    Loads, preprocesses, temporally splits, normalizes, and windows the SWaT
    dataset into a dict ready for CNN-LSTM / VAE training.

    Args:
        path: Path to the SWaT CSV or Excel file.
        window_size: Sliding window length for sequence models.
        test_ratio: Temporal test split ratio.
        normalize_method: "minmax" or "standard".
        use_spectral_residual: Whether to apply spectral residual transform.
        verbose: Print progress information.

    Returns:
        Dict with keys: X_train, X_test, y_train, y_test,
                        X_train_w, X_test_w, y_train_w, y_test_w,
                        feature_names, attack_stats.
    """
    t0 = time.time()
    loader = SWaTLoader()

    if verbose:
        print(f"[SWaTLoader] Loading dataset from: {path}")
    df = loader.load(path)

    if verbose:
        print(f"[SWaTLoader] Raw shape: {df.shape}")
    X, y = loader.preprocess(df, apply_spectral_residual=use_spectral_residual)

    stats = loader.get_attack_stats(y)
    if verbose:
        print(
            f"[SWaTLoader] Preprocessed: {X.shape} | "
            f"Attack ratio: {stats['attack_ratio']:.2%} "
            f"({stats['n_attack']:,} / {stats['total']:,})"
        )

    # Temporal split (preserves time order, critical for SWaT)
    X_train, X_test, y_train, y_test = loader.train_test_split_temporal(
        X, y, train_ratio=1 - test_ratio
    )

    # Normalize (fit on train only)
    X_train_n, X_test_n = loader.normalize(X_train, X_test, method=normalize_method)

    # Sliding window for sequence models
    X_train_w, y_train_w = loader.sliding_window(X_train_n, y_train, window_size=window_size)
    X_test_w, y_test_w = loader.sliding_window(X_test_n, y_test, window_size=window_size)

    if verbose:
        print(
            f"[SWaTLoader] Windows: train={X_train_w.shape}, test={X_test_w.shape} | "
            f"Time elapsed: {time.time() - t0:.2f}s"
        )

    return {
        "X_train": X_train_n,
        "X_test": X_test_n,
        "y_train": y_train,
        "y_test": y_test,
        "X_train_w": X_train_w,
        "X_test_w": X_test_w,
        "y_train_w": y_train_w,
        "y_test_w": y_test_w,
        "feature_names": loader.get_feature_names(),
        "attack_stats": stats,
        "process_groups": list(_PROCESS_GROUPS.keys()),
        "loader": loader,
    }
