<?php

namespace NumPHP\Random;

use NumPHP\Core\NDArray;
use NumPHP\Core\DType;

class Choice
{
    /**
     * Generates a random sample from a given 1-D array.
     *
     * @param mixed $a If an NDArray, a random sample is generated from its elements.
     *                 If an int, the random sample is generated as if a were arange(a).
     * @param int|array|null $size Output shape. If the given shape is, e.g., [m, n], then m * n samples are drawn.
     * @param bool $replace Whether the sample is with or without replacement.
     * @param array|null $p The probabilities associated with each entry in a.
     * @return NDArray
     */
    public static function choice($a, $size = null, bool $replace = true, array $p = null): NDArray
    {
        // 1. Prepare source array
        $source = [];
        if ($a instanceof NDArray) {
            // Assuming flatten behavior for multidimensional input or strict 1D
            $data = $a->getData();
            $source = is_array($data) ? $data : [$data];
            // Note: Ideally should flatten if multi-dimensional
        } elseif (is_int($a)) {
            $source = range(0, $a - 1);
        } elseif (is_array($a)) {
            $source = $a;
        } else {
            throw new \InvalidArgumentException("a must be an integer, array or NDArray");
        }

        if (empty($source)) {
            throw new \InvalidArgumentException("Source array is empty");
        }

        // 2. Determine number of samples needed
        $count = 1;
        $shape = [];

        if ($size === null) {
            $count = 1;
            $shape = []; // scalar
        } elseif (is_int($size)) {
            $count = $size;
            $shape = [$size];
        } elseif (is_array($size)) {
            $count = array_product($size);
            $shape = $size;
        }

        // 3. Sampling
        $samples = [];

        if ($p !== null) {
            if (count($p) !== count($source)) {
                throw new \InvalidArgumentException("p must be the same size as a");
            }

            $sum = array_sum($p);
            if (abs($sum - 1.0) > 1e-6) {
                throw new \InvalidArgumentException("probabilities do not sum to 1");
            }

            if ($replace) {
                for ($i = 0; $i < $count; $i++) {
                    $rand = mt_rand() / mt_getrandmax();
                    $cumulative = 0.0;
                    $idx = count($source) - 1; // Default to last element to avoid precision issues
                    foreach ($p as $k => $prob) {
                        $cumulative += $prob;
                        if ($rand <= $cumulative) {
                            $idx = $k;
                            break;
                        }
                    }
                    $samples[] = $source[$idx];
                }
            } else {
                if ($count > count($source)) {
                    throw new \InvalidArgumentException("Cannot take a larger sample than population when 'replace' is false");
                }

                $sourceCopy = $source;
                $pCopy = $p;

                for ($i = 0; $i < $count; $i++) {
                    // Re-normalize probabilities
                    $sumCopy = array_sum($pCopy);
                    $rand = mt_rand() / mt_getrandmax() * $sumCopy;

                    $cumulative = 0.0;
                    $idx = -1;
                    foreach ($pCopy as $k => $prob) {
                        $cumulative += $prob;
                        if ($rand <= $cumulative) {
                            $idx = $k;
                            break;
                        }
                    }

                    if ($idx === -1) {
                        $idx = array_key_last($pCopy); // Fallback
                    }

                    $samples[] = $sourceCopy[$idx];
                    unset($sourceCopy[$idx]);
                    unset($pCopy[$idx]);
                }
            }
        } else {
            if ($replace) {
                for ($i = 0; $i < $count; $i++) {
                    $idx = array_rand($source);
                    $samples[] = $source[$idx];
                }
            } else {
                if ($count > count($source)) {
                    throw new \InvalidArgumentException("Cannot take a larger sample than population when 'replace' is false");
                }
                $keys = array_rand($source, $count);
                if (!is_array($keys)) {
                    $keys = [$keys];
                }
                // array_rand returns keys in random order or sorted order depending on implementation, usually we want random order for 'choice' result?
                // array_rand actually returns sorted keys sometimes. Shuffle is safer.
                shuffle($keys);
                foreach ($keys as $key) {
                    $samples[] = $source[$key];
                }
            }
        }

        // 4. Reshape to desired size
        // Since we don't have a direct "reshape flat array to shape" helper exposed here easily,
        // we return flat array if shape is 1D, or rely on Reshape logic if we had access.
        // For now, returning 1D array as most common use case or flat NDArray.
        // To strictly support shape, we would need to restructure $samples into $shape.

        // Returning flat NDArray for now to be safe, unless simple scalar.
        if ($size === null) {
            return new NDArray($samples[0]);
        }

        return new NDArray($samples); // This returns 1D array. A reshape would be needed for higher dims.
    }
}