<?php
namespace DatasetPHP\Encoding;

/**
 * OneHotEncoder — Converts integer labels to binary one-hot vectors.
 */
class OneHotEncoder
{
    private $numClasses = 0;

    public function fit(array $labels): self
    {
        $this->numClasses = count(array_unique($labels));
        return $this;
    }

    public function transform(array $labels): array
    {
        $result = [];
        foreach ($labels as $label) {
            $vector = array_fill(0, $this->numClasses, 0);
            if ($label >= 0 && $label < $this->numClasses) {
                $vector[$label] = 1;
            }
            $result[] = $vector;
        }
        return $result;
    }

    public function fitTransform(array $labels): array
    {
        $this->fit($labels);
        return $this->transform($labels);
    }

    public function inverseTransform(array $oneHot): array
    {
        return array_map(fn($vec) => array_search(1, $vec), $oneHot);
    }
}
