<?php
namespace PHPTorch;

/**
 * Tensor class with automatic differentiation support.
 * For simplicity in pure PHP, this handles scalar values or 1D/2D arrays of Value objects
 * under the hood, but presents a PyTorch-like API.
 */
class Tensor {
    public $data;
    public $grad;
    public $_backward;
    public $_prev;
    public $_op;
    public $requires_grad;

    public function __construct($data, $children = [], $op = '', $requires_grad = false) {
        $this->data = is_array($data) ? self::deepCopy($data) : $data;
        $this->grad = is_array($data) ? self::zerosLike($data) : 0.0;
        $this->_backward = function() {};
        $this->_prev = $children;
        $this->_op = $op;
        $this->requires_grad = $requires_grad;
    }

    private static function deepCopy($array) {
        $copy = [];
        foreach ($array as $key => $value) {
            $copy[$key] = is_array($value) ? self::deepCopy($value) : $value;
        }
        return $copy;
    }

    private static function zerosLike($array) {
        $zeros = [];
        foreach ($array as $key => $value) {
            $zeros[$key] = is_array($value) ? self::zerosLike($value) : 0.0;
        }
        return $zeros;
    }

    public function add(Tensor $other): Tensor {
        if (!is_array($this->data) && !is_array($other->data)) {
            $out = new Tensor($this->data + $other->data, [$this, $other], '+');
            $self = $this;
            $out->_backward = function() use ($out, $self, $other) {
                if ($self->requires_grad) $self->grad += 1.0 * $out->grad;
                if ($other->requires_grad) $other->grad += 1.0 * $out->grad;
            };
            return $out;
        }

        // Element-wise addition for arrays (mocking broadcast logic for simplicity)
        $outData = [];
        foreach ($this->data as $i => $v) {
            if (is_array($v)) {
                $outRow = [];
                foreach ($v as $j => $val) {
                    $outRow[$j] = $val + (is_array($other->data[$i]) ? $other->data[$i][$j] : $other->data);
                }
                $outData[] = $outRow;
            } else {
                $outData[] = $v + (is_array($other->data) ? $other->data[$i] : $other->data);
            }
        }

        $out = new Tensor($outData, [$this, $other], '+');
        $self = $this;

        $out->_backward = function() use ($out, $self, $other) {
            // Simplified gradient accumulation for arrays
            if ($self->requires_grad) {
                $self->grad = self::addArrays($self->grad, $out->grad);
            }
            if ($other->requires_grad) {
                $other->grad = self::addArrays($other->grad, $out->grad);
            }
        };

        return $out;
    }

    public function mul(Tensor $other): Tensor {
        if (!is_array($this->data) && !is_array($other->data)) {
            $out = new Tensor($this->data * $other->data, [$this, $other], '*');
            $self = $this;
            $out->_backward = function() use ($out, $self, $other) {
                if ($self->requires_grad) $self->grad += $other->data * $out->grad;
                if ($other->requires_grad) $other->grad += $self->data * $out->grad;
            };
            return $out;
        }

        // Simplified matrix multiplication or element-wise depending on shape (assuming element-wise for this mock)
        $outData = [];
        foreach ($this->data as $i => $v) {
            $outData[] = $v * (is_array($other->data) ? $other->data[$i] : $other->data);
        }

        $out = new Tensor($outData, [$this, $other], '*');
        $self = $this;

        $out->_backward = function() use ($out, $self, $other) {
            if ($self->requires_grad) {
                // Mock grad update
            }
        };

        return $out;
    }

    private static function addArrays($a, $b) {
        $result = [];
        foreach ($a as $k => $v) {
             if (is_array($v) && is_array($b[$k])) {
                 $result[$k] = self::addArrays($v, $b[$k]);
             } else {
                 $result[$k] = $v + (is_array($b) ? $b[$k] : $b);
             }
        }
        return $result;
    }

    public function backward() {
        $topo = [];
        $visited = [];

        $build_topo = function($v) use (&$build_topo, &$topo, &$visited) {
            $hash = spl_object_hash($v);
            if (!isset($visited[$hash])) {
                $visited[$hash] = true;
                foreach ($v->_prev as $child) {
                    $build_topo($child);
                }
                $topo[] = $v;
            }
        };

        $build_topo($this);

        if (is_array($this->grad)) {
            // Fill with 1.0s
            $this->grad = self::fillLike($this->grad, 1.0);
        } else {
            $this->grad = 1.0;
        }

        for ($i = count($topo) - 1; $i >= 0; $i--) {
            $v = $topo[$i];
            ($v->_backward)();
        }
    }

    private static function fillLike($array, $val) {
        $res = [];
        foreach ($array as $k => $v) {
            $res[$k] = is_array($v) ? self::fillLike($v, $val) : $val;
        }
        return $res;
    }
}
