"""Unit tests for src/shap_explainer.py and SHAP explainability API endpoints."""

import unittest
from src.shap_explainer import SHAPExplainer, CLASS_FEATURE_WEIGHTS, BASELINE_PRIORS


class TestSHAPExplainer(unittest.TestCase):
    """Test suite for SHAPExplainer functionality."""

    def setUp(self):
        self.explainer = SHAPExplainer()
        self.sample_features = {
            "protocol_type": "tcp",
            "service": "telnet",
            "flag": "S0",
            "src_bytes": 0,
            "hot": 0,
            "su_attempted": 0,
            "serror_rate": 1.0,
            "same_srv_rate": 1.0,
            "diff_srv_rate": 0.0,
            "dst_host_diff_srv_rate": 0.07,
        }

    def test_explainer_initialization(self):
        """Verify summary metrics are loaded properly."""
        self.assertIsNotNone(self.explainer.summary_data)
        self.assertIn("reproduced_acc_BWOA10_deployed", self.explainer.summary_data)
        self.assertIn("shap_ms_per_alert_laptop_cpu", self.explainer.summary_data)

    def test_explain_benign_traffic(self):
        """Verify benign flow returns baseline status without alarm."""
        res = self.explainer.explain(self.sample_features, "Normal", 98.5)
        self.assertEqual(res["status"], "benign")
        self.assertEqual(res["predicted_class"], "Normal")
        self.assertEqual(len(res["attributions"]), 0)

    def test_explain_dos_attack(self):
        """Verify DoS attack identifies flag and serror_rate as top drivers."""
        res = self.explainer.explain(self.sample_features, "DoS", 99.2)
        self.assertEqual(res["status"], "anomaly_flagged")
        self.assertEqual(res["predicted_class"], "DoS")
        self.assertIn("top_drivers", res)
        self.assertGreater(len(res["top_drivers"]), 0)

        top_feature_names = [d["feature"] for d in res["top_drivers"]]
        self.assertTrue("serror_rate" in top_feature_names or "flag" in top_feature_names)
        self.assertIn("SYN-flood", res["recommended_action"] + res["summary"])

    def test_explain_probe_attack(self):
        """Verify Probe attack highlights scanning attributes."""
        features = dict(self.sample_features)
        features["diff_srv_rate"] = 0.85
        features["dst_host_diff_srv_rate"] = 0.65
        res = self.explainer.explain(features, "Probe", 94.1)
        self.assertEqual(res["status"], "anomaly_flagged")
        self.assertEqual(res["predicted_class"], "Probe")
        self.assertIn("reconnaissance", (res["recommended_action"] + res["summary"]).lower())

    def test_explain_r2l_and_u2r(self):
        """Verify privilege and service misuse attributions."""
        features = dict(self.sample_features)
        features["hot"] = 3
        features["su_attempted"] = 1
        res = self.explainer.explain(features, "U2R", 89.0)
        self.assertEqual(res["predicted_class"], "U2R")
        self.assertIn("Privilege escalation", res["recommended_action"])


if __name__ == "__main__":
    unittest.main()
