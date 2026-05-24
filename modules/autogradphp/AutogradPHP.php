<?php
namespace AutogradPHP;

class AutogradPHP {
    // Main entry point
}

class Value {
    public $data;
    public $grad;
    public $_backward;
    public $_prev;
    public $_op;

    public function __construct($data, $_children = [], $_op = '') {
        $this->data = $data;
        $this->grad = 0.0;
        $this->_backward = function() {};
        $this->_prev = $_children;
        $this->_op = $_op;
    }

    public function add(Value $other): Value {
        $out = new Value($this->data + $other->data, [$this, $other], '+');

        $self = $this; // Fix for PHP not capturing $this in closures easily in older versions, or to avoid ambiguity
        $out->_backward = function() use ($out, $self, $other) {
            $self->grad += 1.0 * $out->grad;
            $other->grad += 1.0 * $out->grad;
        };

        return $out;
    }

    public function mul(Value $other): Value {
        $out = new Value($this->data * $other->data, [$this, $other], '*');

        $self = $this;
        $out->_backward = function() use ($out, $self, $other) {
            $self->grad += $other->data * $out->grad;
            $other->grad += $self->data * $out->grad;
        };

        return $out;
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

        $this->grad = 1.0;
        for ($i = count($topo) - 1; $i >= 0; $i--) {
            $v = $topo[$i];
            ($v->_backward)();
        }
    }
}
