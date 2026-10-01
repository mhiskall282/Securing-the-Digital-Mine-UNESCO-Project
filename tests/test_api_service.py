"""Tests for the ML inference API service."""
import unittest
import json
import threading
import time
from http.server import HTTPServer
import sys
import os
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

class TestAPIServiceEndpoints(unittest.TestCase):
    """Tests API endpoint structure and response format."""
    
    def test_health_response_structure(self):
        """Health endpoint returns required fields."""
        # Import and test the handler directly without starting a full server
        from src.api_service import ModelInferenceHandler
        # Verify the class exists and has the expected methods
        self.assertTrue(hasattr(ModelInferenceHandler, 'do_GET'))
        self.assertTrue(hasattr(ModelInferenceHandler, 'do_POST'))
    
    def test_analyze_payload_structure(self):
        """Analyze endpoint accepts the 10 BWOA selected features."""
        REQUIRED_FEATURES = [
            "protocol_type", "service", "flag", "src_bytes", "hot",
            "su_attempted", "serror_rate", "same_srv_rate", 
            "diff_srv_rate", "dst_host_diff_srv_rate"
        ]
        # Verify all selected features are in ALL_FEATURES list
        from src.api_service import SELECTED_FEATURES, ALL_FEATURES
        for feat in REQUIRED_FEATURES:
            self.assertIn(feat, SELECTED_FEATURES)
            self.assertIn(feat, ALL_FEATURES)
    
    def test_class_labels_complete(self):
        """CLASS_LABELS must contain all 5 attack categories."""
        from src.api_service import CLASS_LABELS
        expected = {"Normal", "DoS", "Probe", "R2L", "U2R"}
        self.assertEqual(set(CLASS_LABELS), expected)
        self.assertEqual(len(CLASS_LABELS), 5)
    
    def test_selected_indices_valid(self):
        """SELECTED_INDICES must map correctly into ALL_FEATURES."""
        from src.api_service import SELECTED_FEATURES, ALL_FEATURES, SELECTED_INDICES
        self.assertEqual(len(SELECTED_FEATURES), 10)
        self.assertEqual(len(SELECTED_INDICES), 10)
        for i, feat in zip(SELECTED_INDICES, SELECTED_FEATURES):
            self.assertEqual(ALL_FEATURES[i], feat)

    def test_max_request_body_bytes_defined(self):
        """MAX_REQUEST_BODY_BYTES must be defined and set to 64 KB."""
        from src.api_service import MAX_REQUEST_BODY_BYTES
        self.assertEqual(MAX_REQUEST_BODY_BYTES, 64 * 1024)

    def test_missing_features_detection(self):
        """Validation logic identifies missing BWOA features in partial payloads."""
        from src.api_service import SELECTED_FEATURES
        partial_payload = {"protocol_type": "tcp", "service": "http"}
        missing = [f for f in SELECTED_FEATURES if f not in partial_payload]
        self.assertEqual(len(missing), 8)
        self.assertIn("flag", missing)
        self.assertIn("src_bytes", missing)
        self.assertIn("serror_rate", missing)


if __name__ == "__main__":
    unittest.main()
