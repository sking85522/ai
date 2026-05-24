<?php

namespace NumPHP\Random;

use NumPHP\Core\NDArray;

class Shuffle
{
    /**
     * Shuffle array in-place along the first axis.
     *
     * @param NDArray $x
     * @return void
     */
    public static function shuffle(NDArray $x): void
    {
        $data = $x->getData();
        if (!is_array($data)) {
            return;
        }

        shuffle($data);
        $x->setData($data);
    }
}
