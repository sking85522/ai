# 🧠 NeuralPHP — Neural Network Library

> **Python Equivalent:** TensorFlow / Keras
> **Purpose:** Build and train neural networks for deep learning

---

## Quick Start
```php
use NeuralPHP\NeuralPHP as nn;

$model = nn::Sequential();
$model->add(nn::Dense(2, 4, nn::getActivation('relu')));
$model->add(nn::Dropout(0.3));
$model->add(nn::Dense(4, 1, nn::getActivation('sigmoid')));
$model->compile(nn::getLoss('bce'), nn::getOptimizer('adam', 0.001));
$model->fit($X, $y, epochs: 100);
$output = $model->predict($X_test);
```

---

## Models

### `Sequential()` — Sequential Neural Network
Stack of layers executed in order.

```php
$model = nn::Sequential();
$model->add($layer1);
$model->add($layer2);
$model->compile($loss, $optimizer);
$model->fit($X, $y, epochs: 50);
```

---

## Layers

### `Dense(inputSize, outputSize, activation)` — Fully Connected Layer
Every input neuron connects to every output neuron.

```php
nn::Dense(784, 128, nn::getActivation('relu'));   // 784 inputs → 128 outputs
nn::Dense(128, 10, nn::getActivation('softmax')); // 128 inputs → 10 classes
```

### `Dropout(rate)` — Dropout Regularization
Randomly zeroes neurons during training to prevent overfitting.

```php
nn::Dropout(0.5);  // Drops 50% of neurons during training
nn::Dropout(0.2);  // Drops 20%
```

### `BatchNorm(size, epsilon)` — Batch Normalization
Normalizes layer inputs for faster convergence.

```php
nn::BatchNorm(128);        // Normalize 128-dimensional input
nn::BatchNorm(64, 1e-6);  // Custom epsilon
```

---

## Activation Functions

| Function | Use Case | Code |
|----------|----------|------|
| **ReLU** | Hidden layers (default) | `nn::getActivation('relu')` |
| **Sigmoid** | Binary output (0-1) | `nn::getActivation('sigmoid')` |
| **Tanh** | Normalized output (-1 to 1) | `nn::getActivation('tanh')` |
| **Softmax** | Multi-class output | `nn::getActivation('softmax')` |
| **LeakyReLU** | Avoids dying ReLU | `nn::getActivation('leaky_relu')` |

---

## Loss Functions

| Function | Use Case | Code |
|----------|----------|------|
| **MSE** | Regression | `nn::getLoss('mse')` |
| **CrossEntropy** | Multi-class classification | `nn::getLoss('cross_entropy')` |
| **BinaryCrossEntropy** | Binary classification | `nn::getLoss('bce')` |

---

## Optimizers

| Optimizer | Description | Code |
|-----------|-------------|------|
| **SGD** | Basic gradient descent | `nn::getOptimizer('sgd', 0.01)` |
| **Adam** | Adaptive (best default choice) | `nn::getOptimizer('adam', 0.001)` |
| **RMSprop** | Good for RNNs | `nn::getOptimizer('rmsprop', 0.001)` |

---

## Full Example — XOR Problem

```php
use NeuralPHP\NeuralPHP as nn;

$X = [[0,0], [0,1], [1,0], [1,1]];
$y = [[0], [1], [1], [0]];

$model = nn::Sequential();
$model->add(nn::Dense(2, 4, nn::getActivation('relu')));
$model->add(nn::Dense(4, 1, nn::getActivation('sigmoid')));
$model->compile(nn::getLoss('bce'), nn::getOptimizer('adam', 0.01));
$model->fit($X, $y, epochs: 500);

$result = $model->predict([[1, 0]]); // Should be close to 1
```
