<?php
namespace SpeechPHP\Analysis;

/**
 * Pitch detection using autocorrelation method.
 */
class PitchDetector
{
    /**
     * Detect fundamental frequency (pitch) from audio signal.
     * @param array $signal Audio data
     * @param int $sampleRate Sample rate in Hz
     * @param float $minFreq Minimum expected frequency (Hz)
     * @param float $maxFreq Maximum expected frequency (Hz)
     * @return array ['frequency' => Hz, 'period' => samples, 'confidence' => 0-1]
     */
    public static function detect(array $signal, int $sampleRate = 16000, float $minFreq = 80, float $maxFreq = 600): array
    {
        $n = count($signal);
        $minLag = (int)floor($sampleRate / $maxFreq);
        $maxLag = (int)ceil($sampleRate / $minFreq);
        $maxLag = min($maxLag, intval($n / 2));

        // Autocorrelation
        $bestCorrelation = -1;
        $bestLag = 0;

        // Normalize energy
        $energy = 0;
        foreach ($signal as $s) $energy += $s * $s;
        if ($energy < 1e-10) {
            return ['frequency' => 0.0, 'period' => 0, 'confidence' => 0.0];
        }

        for ($lag = $minLag; $lag <= $maxLag; $lag++) {
            $correlation = 0;
            for ($i = 0; $i < $n - $lag; $i++) {
                $correlation += $signal[$i] * $signal[$i + $lag];
            }
            $correlation /= ($n - $lag);

            if ($correlation > $bestCorrelation) {
                $bestCorrelation = $correlation;
                $bestLag = $lag;
            }
        }

        $frequency = ($bestLag > 0) ? $sampleRate / $bestLag : 0.0;
        $confidence = max(0, min(1, $bestCorrelation / sqrt($energy / $n)));

        return [
            'frequency' => round($frequency, 2),
            'period' => $bestLag,
            'confidence' => round($confidence, 4),
        ];
    }

    /**
     * Detect pitch over multiple frames.
     * @return array of pitch results per frame
     */
    public static function trackPitch(array $signal, int $sampleRate = 16000, int $frameSize = 1024, int $hopSize = 512): array
    {
        $pitches = [];
        $n = count($signal);
        for ($i = 0; $i + $frameSize <= $n; $i += $hopSize) {
            $frame = array_slice($signal, $i, $frameSize);
            $pitches[] = self::detect($frame, $sampleRate);
        }
        return $pitches;
    }
}
