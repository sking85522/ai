<?php
namespace DatasetPHP\Encoding;

/**
 * LabelEncoder — Encodes categorical labels into integers.
 */
class LabelEncoder
{
    private $mapping = [];
    private $inverse = [];

    public function fit(array $labels): self
    {
        $unique = array_values(array_unique($labels));
        sort($unique);
        $this->mapping = array_flip($unique);
        $this->inverse = $unique;
        return $this;
    }

    public function transform(array $labels): array
    {
        return array_map(fn($l) => $this->mapping[$l] ?? -1, $labels);
    }

    public function fitTransform(array $labels): array
    {
        $this->fit($labels);
        return $this->transform($labels);
    }

    public function inverseTransform(array $encoded): array
    {
        return array_map(fn($e) => $this->inverse[$e] ?? null, $encoded);
    }

    public function getClasses(): array { return $this->inverse; }
}
