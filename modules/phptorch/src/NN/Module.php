<?php
namespace PHPTorch\NN;

abstract class Module {
    public function __invoke(...$args) {
        return $this->forward(...$args);
    }

    abstract public function forward(...$args): \PHPTorch\Tensor;

    public function parameters(): array {
        $params = [];
        $props = get_object_vars($this);
        foreach ($props as $prop) {
            if ($prop instanceof \PHPTorch\Tensor && $prop->requires_grad) {
                $params[] = $prop;
            } elseif ($prop instanceof Module) {
                $params = array_merge($params, $prop->parameters());
            } elseif (is_array($prop)) {
                foreach ($prop as $item) {
                     if ($item instanceof Module) {
                         $params = array_merge($params, $item->parameters());
                     }
                }
            }
        }
        return $params;
    }
}
