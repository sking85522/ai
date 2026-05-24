<?php
namespace PHPTorch\Optim;

abstract class Optimizer {
    protected $params;
    protected $defaults;

    public function __construct(array $params, array $defaults = []) {
        $this->params = $params;
        $this->defaults = $defaults;
    }

    public function zero_grad() {
        foreach ($this->params as $p) {
            if ($p->grad !== null) {
                $p->grad = is_array($p->grad) ? $this->zerosLike($p->grad) : 0.0;
            }
        }
    }

    protected function zerosLike($array) {
        $zeros = [];
        foreach ($array as $key => $value) {
            $zeros[$key] = is_array($value) ? $this->zerosLike($value) : 0.0;
        }
        return $zeros;
    }

    abstract public function step();
}
