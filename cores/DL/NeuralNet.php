<?php
namespace Core\DL;

require_once __DIR__ . '/NeuralNetwork.php';

// Link existing project module for fallback/advanced use
if (file_exists(dirname(__DIR__, 2) . '/modules/neuralphp/autoload.php')) {
    require_once dirname(__DIR__, 2) . '/modules/neuralphp/autoload.php';
}

use NeuralPHP\NeuralPHP;
use NeuralPHP\Models\Sequential;

class NeuralNet {
    private ?Sequential $moduleModel = null;
    private ?NeuralNetwork $nativeModel = null;
    private string $mode = 'native'; // 'native' or 'module'

    public function __construct(string $mode = 'native') {
        $this->mode = $mode;
        
        if ($this->mode === 'module' && class_exists('NeuralPHP\NeuralPHP')) {
            $this->moduleModel = NeuralPHP::Sequential();
            $this->buildModuleBrain();
        } else {
            $this->nativeModel = new NeuralNetwork(0.1);
            $this->buildNativeBrain();
        }
    }

    private function buildNativeBrain(): void {
        // Native Architecture: 2 Input -> 4 Hidden -> 1 Output (for XOR/Binary tasks)
        $this->nativeModel->addLayer(2, 4, 'sigmoid');
        $this->nativeModel->addLayer(4, 1, 'sigmoid');
    }

    private function buildModuleBrain(): void {
        if (!$this->moduleModel) return;
        $this->moduleModel->add(NeuralPHP::Dense(2, 4, 'relu'));
        $this->moduleModel->add(NeuralPHP::Dense(4, 1, 'sigmoid'));
        $this->moduleModel->compile('mse', 'adam');
    }

    public function train(array $X, array $y, int $epochs = 100): bool {
        if ($this->mode === 'module' && $this->moduleModel) {
            $this->moduleModel->fit($X, $y, $epochs);
            return true;
        } elseif ($this->nativeModel) {
            // Native training usually needs more epochs for simple SGD
            $this->nativeModel->train($X, $y, $epochs * 10); 
            return true;
        }
        return false;
    }

    public function predict(array $X): array {
        if ($this->mode === 'module' && $this->moduleModel) {
            return $this->moduleModel->predict($X);
        } elseif ($this->nativeModel) {
            return $this->nativeModel->predict($X[0]); // Predict first sample
        }
        return [];
    }
}
