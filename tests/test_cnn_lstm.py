"""Unit tests for the CNN-LSTM deep learning model architecture.

This module contains test cases to verify the construction of the hybrid model layers,
input shapes, and predictions.
"""

import unittest
import numpy as np
import tensorflow as tf
from src.models.cnn_lstm import build_cnn_lstm


class TestCNNLSTMModel(unittest.TestCase):
    """Tests the Keras architecture assembly and forward propagation."""

    def test_model_construction(self) -> None:
        """Verifies that the compiled model has the expected output layers and shape."""
        input_shape = (10, 41)  # sequence_length = 10, features = 41
        n_classes = 2
        
        model = build_cnn_lstm(
            input_shape=input_shape,
            n_classes=n_classes,
            filters=16,
            kernel_size=3,
            lstm_units=32,
            dropout_rate=0.2
        )
        
        self.assertIsInstance(model, tf.keras.Model)
        self.assertEqual(model.input_shape, (None, 10, 41))
        self.assertEqual(model.output_shape, (None, 2))

    def test_forward_pass(self) -> None:
        """Checks prediction output dimensions for dummy input batches."""
        input_shape = (5, 20)
        n_classes = 5
        
        model = build_cnn_lstm(
            input_shape=input_shape,
            n_classes=n_classes,
            filters=8,
            kernel_size=3,
            lstm_units=16,
            dropout_rate=0.1
        )
        
        dummy_inputs = np.random.rand(4, 5, 20)  # batch size = 4
        predictions = model.predict(dummy_inputs)
        
        self.assertEqual(predictions.shape, (4, 5))
        # Ensure it outputs probability distributions summing to 1 (softmax)
        np.testing.assert_allclose(np.sum(predictions, axis=-1), np.ones(4), rtol=1e-5)


class TestCNNLSTMV4(unittest.TestCase):
    """Tests the CNN-LSTM v4 strengthened architecture."""

    def test_v4_construction_and_forward_pass(self) -> None:
        """Verifies v4 input/output shape, attention layer presence, and forward pass."""
        from src.models.cnn_lstm import build_cnn_lstm_v4
        input_shape = (10, 1)
        n_classes = 5

        model = build_cnn_lstm_v4(
            input_shape=input_shape,
            n_classes=n_classes,
            filters=16,
            kernel_size=3,
            lstm_units=32,
            dropout_rate=0.2
        )

        self.assertIsInstance(model, tf.keras.Model)
        self.assertEqual(model.input_shape, (None, 10, 1))
        self.assertEqual(model.output_shape, (None, 5))

        # Check attention mechanism exists in model layers (Softmax or Multiply)
        layer_types = [layer.__class__.__name__ for layer in model.layers]
        self.assertTrue(
            "Softmax" in layer_types or "Multiply" in layer_types,
            "Attention components missing from model layers"
        )

        dummy_inputs = np.random.rand(4, 10, 1)
        predictions = model.predict(dummy_inputs, verbose=0)
        self.assertEqual(predictions.shape, (4, 5))
        np.testing.assert_allclose(np.sum(predictions, axis=-1), np.ones(4), rtol=1e-4)


class TestCNNLSTMWithAttention(unittest.TestCase):
    """Tests the CNN-LSTM with attention architecture."""

    def test_attention_model_construction(self) -> None:
        """Verifies attention model input/output shape and forward pass."""
        from src.models.cnn_lstm import build_cnn_lstm_with_attention
        input_shape = (10, 1)
        n_classes = 5

        model = build_cnn_lstm_with_attention(
            input_shape=input_shape,
            n_classes=n_classes,
            filters=16,
            kernel_size=3,
            lstm_units=32,
            dropout_rate=0.2
        )

        self.assertIsInstance(model, tf.keras.Model)
        self.assertEqual(model.input_shape, (None, 10, 1))
        self.assertEqual(model.output_shape, (None, 5))

        layer_types = [layer.__class__.__name__ for layer in model.layers]
        self.assertTrue("Softmax" in layer_types or "Multiply" in layer_types)

        dummy_inputs = np.random.rand(4, 10, 1)
        predictions = model.predict(dummy_inputs, verbose=0)
        self.assertEqual(predictions.shape, (4, 5))
        np.testing.assert_allclose(np.sum(predictions, axis=-1), np.ones(4), rtol=1e-4)


if __name__ == "__main__":
    unittest.main()

