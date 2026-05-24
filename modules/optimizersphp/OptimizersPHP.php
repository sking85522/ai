<?php
namespace OptimizersPHP;

class OptimizersPHP {
    // Main entry point
}

class SGD {
    private $parameters;
    private $lr;

    public function __construct(array $parameters, float $lr = 0.01) {
        $this->parameters = $parameters; // Array of AutogradPHP\Value objects
        $this->lr = $lr;
    }

    public function step() {
        foreach ($this->parameters as $p) {
            $p->data -= $this->lr * $p->grad;
        }
    }

    public function zero_grad() {
        foreach ($this->parameters as $p) {
            $p->grad = 0.0;
        }
    }
}

class Adam {
    private $parameters;
    private $lr;
    private $beta1;
    private $beta2;
    private $epsilon;
    private $m = [];
    private $v = [];
    private $t = 0;

    public function __construct(array $parameters, float $lr = 0.001, float $beta1 = 0.9, float $beta2 = 0.999, float $epsilon = 1e-8) {
        $this->parameters = $parameters;
        $this->lr = $lr;
        $this->beta1 = $beta1;
        $this->beta2 = $beta2;
        $this->epsilon = $epsilon;

        foreach ($parameters as $i => $p) {
            $this->m[$i] = 0.0;
            $this->v[$i] = 0.0;
        }
    }

    public function step() {
        $this->t += 1;
        foreach ($this->parameters as $i => $p) {
            $grad = $p->grad;
            $this->m[$i] = $this->beta1 * $this->m[$i] + (1 - $this->beta1) * $grad;
            $this->v[$i] = $this->beta2 * $this->v[$i] + (1 - $this->beta2) * ($grad * $grad);

            $m_hat = $this->m[$i] / (1 - pow($this->beta1, $this->t));
            $v_hat = $this->v[$i] / (1 - pow($this->beta2, $this->t));

            $p->data -= $this->lr * $m_hat / (sqrt($v_hat) + $this->epsilon);
        }
    }

    public function zero_grad() {
        foreach ($this->parameters as $p) {
            $p->grad = 0.0;
        }
    }
}
