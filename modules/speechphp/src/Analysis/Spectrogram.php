<?php
namespace SpeechPHP\Analysis;

/**
 * Spectrogram — Generates time-frequency representation of audio signals.
 */
class Spectrogram
{
    /**
     * Compute spectrogram (STFT magnitude) from audio signal.
     * @param array $signal Audio amplitude data
     * @param int $frameSize Size of each FFT frame
     * @param int $hopSize Hop between frames
     * @return array ['data' => 2D [time][freq], 'timeAxis' => [], 'freqAxis' => []]
     */
    public static function compute(array $signal, int $frameSize = 512, int $hopSize = 256, int $sampleRate = 16000): array
    {
        $n = count($signal);
        $spectrogram = [];
        $timeAxis = [];
        $frame_idx = 0;

        for ($i = 0; $i + $frameSize <= $n; $i += $hopSize) {
            $frame = array_slice($signal, $i, $frameSize);

            // Hamming window
            for ($j = 0; $j < $frameSize; $j++) {
                $frame[$j] *= 0.54 - 0.46 * cos(2 * M_PI * $j / ($frameSize - 1));
            }

            // FFT magnitudes
            $halfN = intval($frameSize / 2);
            $magnitudes = [];
            for ($k = 0; $k <= $halfN; $k++) {
                $real = 0;
                $imag = 0;
                for ($t = 0; $t < $frameSize; $t++) {
                    $angle = 2 * M_PI * $k * $t / $frameSize;
                    $real += $frame[$t] * cos($angle);
                    $imag -= $frame[$t] * sin($angle);
                }
                $magnitudes[] = sqrt($real * $real + $imag * $imag) / $frameSize;
            }

            $spectrogram[] = $magnitudes;
            $timeAxis[] = $frame_idx * $hopSize / $sampleRate;
            $frame_idx++;
        }

        // Frequency axis
        $halfN = intval($frameSize / 2);
        $freqAxis = [];
        for ($k = 0; $k <= $halfN; $k++) {
            $freqAxis[] = $k * $sampleRate / $frameSize;
        }

        return [
            'data' => $spectrogram,
            'timeAxis' => $timeAxis,
            'freqAxis' => $freqAxis,
        ];
    }

    /**
     * Convert spectrogram to dB scale.
     */
    public static function toDecibels(array $spectrogram): array
    {
        $dbSpec = [];
        foreach ($spectrogram as $frame) {
            $dbFrame = [];
            foreach ($frame as $val) {
                $dbFrame[] = 20 * log10(max($val, 1e-10));
            }
            $dbSpec[] = $dbFrame;
        }
        return $dbSpec;
    }
}
