<?php
namespace PHPTorch\Optim;

class Adam extends Optimizer {
    // Simplified Adam mock
    public function __construct(array $params, float $lr = 0.001) {
        parent::__construct($params, ['lr' => $lr]);
    }

    public function step() {
        $lr = $this->defaults['lr'];
        foreach ($this->params as $p) {
            if ($p->grad === null) continue;

            if (is_array($p->data) && is_array($p->grad)) {
                 $p->data = $this->updateArray($p->data, $p->grad, $lr);
            } else {
                 $p->data -= $lr * $p->grad; // Simple fallback to SGD for mock
            }
        }
    }

    private function updateArray($data, $grad, $lr) {
        $res = [];
        foreach ($data as $k => $v) {
            if (is_array($v) && is_array($grad[$k])) {
                $res[$k] = $this->updateArray($v, $grad[$k], $lr);
            } else {
                $res[$k] = $v - $lr * (is_array($grad) ? $grad[$k] : $grad);
            }
        }
        return $res;
    }
}
