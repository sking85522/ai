<?php

namespace SpeechPHP\Features;

use NumPHP\Core\NDArray;

class AudioFeatures
{
    public static function zcr($data): float
    {
        $dataArr = ($data instanceof NDArray) ? $data->getData() : $data;
        $crossings = 0;
        $n = count($dataArr);

        if ($n <= 1) return 0.0;

        for ($i = 1; $i < $n; $i++) {
            if (($dataArr[$i] > 0 && $dataArr[$i - 1] <= 0) ||
                ($dataArr[$i] <= 0 && $dataArr[$i - 1] > 0)) {
                $crossings++;
            }
        }

        return $crossings / $n;
    }

    public static function rms($data): float
    {
        $dataArr = ($data instanceof NDArray) ? $data->getData() : $data;
        $sumSq = 0.0;
        $n = count($dataArr);

        if ($n === 0) return 0.0;

        foreach ($dataArr as $val) {
            $sumSq += pow($val, 2);
        }

        return sqrt($sumSq / $n);
    }
}
