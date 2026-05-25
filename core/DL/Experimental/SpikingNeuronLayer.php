<?php
namespace Core\DL\Experimental;

/**
 * HRITIK AI - SPIKING NEURON LAYER
 * Simulates biological neurons that fire based on accumulated electrical potential.
 */
class SpikingNeuronLayer {
    
    private array $thresholds = [];
    private array $potentials = [];

    public function __construct(int $size) {
        $this->thresholds = array_fill(0, $size, 1.0);
        $this->potentials = array_fill(0, $size, 0.0);
    }

    /**
     * Updates potentials and fires spikes if threshold is reached.
     */
    public function fire(array $inputs): array {
        $spikes = [];
        foreach ($inputs as $i => $v) {
            $this->potentials[$i] += $v;
            if ($this->potentials[$i] >= $this->thresholds[$i]) {
                $spikes[$i] = 1.0;
                $this->potentials[$i] = 0.0; // Reset
            } else {
                $spikes[$i] = 0.0;
            }
        }
        return $spikes;
    }
}
