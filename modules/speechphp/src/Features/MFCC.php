<?php
namespace SpeechPHP\Features;

/**
 * MFCC — Mel-Frequency Cepstral Coefficients.
 * The gold standard feature for speech recognition tasks.
 */
class MFCC
{
    private $sampleRate;
    private $numCoeffs;
    private $frameSize;
    private $hopSize;
    private $numFilters;

    public function __construct(int $sampleRate = 16000, int $numCoeffs = 13, int $frameSize = 512, int $hopSize = 256, int $numFilters = 26)
    {
        $this->sampleRate = $sampleRate;
        $this->numCoeffs = $numCoeffs;
        $this->frameSize = $frameSize;
        $this->hopSize = $hopSize;
        $this->numFilters = $numFilters;
    }

    /**
     * Compute MFCCs for a given audio signal.
     * @param array $signal Audio amplitude array
     * @return array 2D array [frames][coefficients]
     */
    public function compute(array $signal): array
    {
        $frames = $this->frameSignal($signal);
        $mfccs = [];

        foreach ($frames as $frame) {
            // 1. Apply Hamming window
            $windowed = $this->hammingWindow($frame);

            // 2. FFT
            $spectrum = $this->fft($windowed);

            // 3. Power spectrum
            $powerSpectrum = array_map(fn($v) => $v * $v, $spectrum);

            // 4. Mel filter bank
            $melEnergies = $this->melFilterBank($powerSpectrum);

            // 5. Log
            $logMel = array_map(fn($v) => log(max($v, 1e-10)), $melEnergies);

            // 6. DCT to get MFCCs
            $coeffs = $this->dct($logMel);
            $mfccs[] = array_slice($coeffs, 0, $this->numCoeffs);
        }

        return $mfccs;
    }

    private function frameSignal(array $signal): array
    {
        $frames = [];
        $n = count($signal);
        for ($i = 0; $i + $this->frameSize <= $n; $i += $this->hopSize) {
            $frames[] = array_slice($signal, $i, $this->frameSize);
        }
        return $frames;
    }

    private function hammingWindow(array $frame): array
    {
        $n = count($frame);
        $windowed = [];
        for ($i = 0; $i < $n; $i++) {
            $windowed[] = $frame[$i] * (0.54 - 0.46 * cos(2 * M_PI * $i / ($n - 1)));
        }
        return $windowed;
    }

    private function fft(array $signal): array
    {
        $n = count($signal);
        $magnitudes = [];
        $halfN = intval($n / 2);
        for ($k = 0; $k <= $halfN; $k++) {
            $real = 0;
            $imag = 0;
            for ($t = 0; $t < $n; $t++) {
                $angle = 2 * M_PI * $k * $t / $n;
                $real += $signal[$t] * cos($angle);
                $imag -= $signal[$t] * sin($angle);
            }
            $magnitudes[] = sqrt($real * $real + $imag * $imag) / $n;
        }
        return $magnitudes;
    }

    private function hzToMel(float $hz): float
    {
        return 2595 * log10(1 + $hz / 700);
    }

    private function melToHz(float $mel): float
    {
        return 700 * (pow(10, $mel / 2595) - 1);
    }

    private function melFilterBank(array $powerSpectrum): array
    {
        $nfft = (count($powerSpectrum) - 1) * 2;
        $lowMel = $this->hzToMel(0);
        $highMel = $this->hzToMel($this->sampleRate / 2);

        $melPoints = [];
        for ($i = 0; $i <= $this->numFilters + 1; $i++) {
            $melPoints[] = $lowMel + ($highMel - $lowMel) * $i / ($this->numFilters + 1);
        }

        $bins = array_map(fn($mel) => (int)floor(($nfft + 1) * $this->melToHz($mel) / $this->sampleRate), $melPoints);

        $energies = [];
        for ($m = 1; $m <= $this->numFilters; $m++) {
            $energy = 0.0;
            for ($k = $bins[$m - 1]; $k <= $bins[$m + 1] && $k < count($powerSpectrum); $k++) {
                if ($k < $bins[$m]) {
                    $weight = ($k - $bins[$m - 1]) / max(1, $bins[$m] - $bins[$m - 1]);
                } else {
                    $weight = ($bins[$m + 1] - $k) / max(1, $bins[$m + 1] - $bins[$m]);
                }
                $energy += max(0, $weight) * ($powerSpectrum[$k] ?? 0);
            }
            $energies[] = $energy;
        }

        return $energies;
    }

    private function dct(array $input): array
    {
        $n = count($input);
        $coeffs = [];
        for ($k = 0; $k < $n; $k++) {
            $sum = 0.0;
            for ($i = 0; $i < $n; $i++) {
                $sum += $input[$i] * cos(M_PI * $k * (2 * $i + 1) / (2 * $n));
            }
            $coeffs[] = $sum;
        }
        return $coeffs;
    }
}
