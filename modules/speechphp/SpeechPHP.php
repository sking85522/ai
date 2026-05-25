<?php

namespace SpeechPHP;

use SpeechPHP\Core\WavParser;
use SpeechPHP\Synthesis\WaveGenerator;
use SpeechPHP\Features\AudioFeatures;
use SpeechPHP\Features\MFCC;
use SpeechPHP\Analysis\Spectrogram;
use SpeechPHP\Analysis\PitchDetector;
use NumPHP\Core\NDArray;

class SpeechPHP
{
    // ──────────── I/O ────────────

    public static function read(string $filepath): array
    {
        return WavParser::read($filepath);
    }

    public static function write(string $filepath, int $sampleRate, $data): bool
    {
        return WaveGenerator::write($filepath, $sampleRate, $data);
    }

    // ──────────── Synthesis ────────────

    public static function generate_tone(float $frequency, float $duration, int $sampleRate = 44100): NDArray
    {
        return WaveGenerator::generateTone($frequency, $duration, $sampleRate);
    }

    // ──────────── Basic Features ────────────

    public static function zero_crossing_rate($data): float
    {
        return AudioFeatures::zcr($data);
    }

    public static function rms_energy($data): float
    {
        return AudioFeatures::rms($data);
    }

    // ──────────── MFCC ────────────

    /**
     * Compute Mel-Frequency Cepstral Coefficients.
     * @param array $signal Audio amplitude data
     * @param int $sampleRate Sample rate in Hz
     * @param int $numCoeffs Number of MFCC coefficients (default 13)
     * @return array 2D [frames][coefficients]
     */
    public static function mfcc(array $signal, int $sampleRate = 16000, int $numCoeffs = 13): array
    {
        $extractor = new MFCC($sampleRate, $numCoeffs);
        return $extractor->compute($signal);
    }

    /**
     * Get an MFCC extractor instance for custom configuration.
     */
    public static function MFCCExtractor(int $sampleRate = 16000, int $numCoeffs = 13, int $frameSize = 512): MFCC
    {
        return new MFCC($sampleRate, $numCoeffs, $frameSize);
    }

    // ──────────── Spectrogram ────────────

    /**
     * Generate spectrogram from audio signal.
     * @return array ['data' => 2D, 'timeAxis' => [], 'freqAxis' => []]
     */
    public static function spectrogram(array $signal, int $frameSize = 512, int $hopSize = 256, int $sampleRate = 16000): array
    {
        return Spectrogram::compute($signal, $frameSize, $hopSize, $sampleRate);
    }

    /**
     * Convert spectrogram to dB scale.
     */
    public static function spectrogram_db(array $spectrogramData): array
    {
        return Spectrogram::toDecibels($spectrogramData);
    }

    // ──────────── Pitch Detection ────────────

    /**
     * Detect pitch (fundamental frequency) of an audio signal.
     * @return array ['frequency' => Hz, 'period' => samples, 'confidence' => 0-1]
     */
    public static function detect_pitch(array $signal, int $sampleRate = 16000): array
    {
        return PitchDetector::detect($signal, $sampleRate);
    }

    /**
     * Track pitch over time (multi-frame).
     * @return array of pitch results per frame
     */
    public static function pitch_track(array $signal, int $sampleRate = 16000, int $frameSize = 1024): array
    {
        return PitchDetector::trackPitch($signal, $sampleRate, $frameSize);
    }
}
