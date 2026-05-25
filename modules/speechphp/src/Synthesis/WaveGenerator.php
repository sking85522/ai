<?php

namespace SpeechPHP\Synthesis;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class WaveGenerator
{
    /**
     * Write normalized float amplitude data (-1.0 to 1.0) to a 16-bit PCM Mono .wav file.
     */
    public static function write(string $filepath, int $sampleRate, $data): bool
    {
        $dataArr = ($data instanceof NDArray) ? $data->getData() : $data;
        $numSamples = count($dataArr);

        $numChannels = 1;
        $bitsPerSample = 16;
        $byteRate = ($sampleRate * $numChannels * $bitsPerSample) / 8;
        $blockAlign = ($numChannels * $bitsPerSample) / 8;

        $dataLength = $numSamples * $numChannels * ($bitsPerSample / 8);
        $fileSize = 36 + $dataLength;

        $fp = fopen($filepath, 'wb');
        if (!$fp) return false;

        // RIFF Header
        fwrite($fp, 'RIFF');
        fwrite($fp, pack('V', $fileSize));
        fwrite($fp, 'WAVE');

        // fmt Chunk
        fwrite($fp, 'fmt ');
        fwrite($fp, pack('V', 16)); // Chunk size
        fwrite($fp, pack('v', 1));  // Audio format (1 = PCM)
        fwrite($fp, pack('v', $numChannels));
        fwrite($fp, pack('V', $sampleRate));
        fwrite($fp, pack('V', $byteRate));
        fwrite($fp, pack('v', $blockAlign));
        fwrite($fp, pack('v', $bitsPerSample));

        // data Chunk
        fwrite($fp, 'data');
        fwrite($fp, pack('V', $dataLength));

        // Write samples
        $maxInt16 = 32767;
        foreach ($dataArr as $val) {
            // Clip to prevent overflow
            $val = max(-1.0, min(1.0, $val));
            $intVal = (int) round($val * $maxInt16);
            fwrite($fp, pack('s', $intVal));
        }

        fclose($fp);
        return true;
    }

    /**
     * Generate a sine wave tone array.
     */
    public static function generateTone(float $frequency, float $duration, int $sampleRate = 44100): NDArray
    {
        $numSamples = (int) ($duration * $sampleRate);
        $data = [];

        for ($i = 0; $i < $numSamples; $i++) {
            $t = $i / $sampleRate;
            // Generate sine wave amplitude: sin(2 * pi * f * t)
            $data[] = sin(2 * M_PI * $frequency * $t);
        }

        return np::array($data);
    }
}
