<?php

namespace SpeechPHP\Core;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class WavParser
{
    /**
     * Reads a uncompressed PCM .wav file.
     * Currently supports 16-bit Mono.
     */
    public static function read(string $filepath): array
    {
        if (!file_exists($filepath)) {
            throw new \Exception("File not found: $filepath");
        }

        $fp = fopen($filepath, 'rb');

        // Read RIFF Header
        $riff = fread($fp, 4);
        if ($riff !== 'RIFF') {
            fclose($fp);
            throw new \Exception("Not a valid RIFF file.");
        }

        $chunkSize = unpack('V', fread($fp, 4))[1];
        $format = fread($fp, 4);

        if ($format !== 'WAVE') {
            fclose($fp);
            throw new \Exception("Not a valid WAVE file.");
        }

        // Find fmt chunk
        while (!feof($fp)) {
            $chunkId = fread($fp, 4);
            $chunkLength = unpack('V', fread($fp, 4))[1];

            if ($chunkId === 'fmt ') {
                $fmtData = fread($fp, $chunkLength);
                $audioFormat = unpack('v', substr($fmtData, 0, 2))[1]; // 1 for PCM
                $numChannels = unpack('v', substr($fmtData, 2, 2))[1];
                $sampleRate = unpack('V', substr($fmtData, 4, 4))[1];
                $byteRate = unpack('V', substr($fmtData, 8, 4))[1];
                $blockAlign = unpack('v', substr($fmtData, 12, 2))[1];
                $bitsPerSample = unpack('v', substr($fmtData, 14, 2))[1];

                if ($audioFormat !== 1) {
                    throw new \Exception("Only uncompressed PCM WAV files are supported.");
                }
                if ($numChannels !== 1) {
                    throw new \Exception("Only mono WAV files are currently supported.");
                }
                if ($bitsPerSample !== 16) {
                    throw new \Exception("Only 16-bit WAV files are supported.");
                }
                break;
            } else {
                // Skip chunk
                fseek($fp, $chunkLength, SEEK_CUR);
            }
        }

        // Find data chunk
        fseek($fp, 12, SEEK_SET); // Reset after RIFF header
        $dataStr = '';
        while (!feof($fp)) {
            $chunkId = fread($fp, 4);
            $chunkLength = unpack('V', fread($fp, 4))[1];

            if ($chunkId === 'data') {
                $dataStr = fread($fp, $chunkLength);
                break;
            } else {
                fseek($fp, $chunkLength, SEEK_CUR);
            }
        }

        fclose($fp);

        if (empty($dataStr)) {
            throw new \Exception("No audio data found in file.");
        }

        // Convert binary data to integers (16-bit signed little-endian)
        $numSamples = strlen($dataStr) / 2;
        $unpacked = unpack('s*', $dataStr);

        // Normalize to float between -1.0 and 1.0
        $normalizedData = [];
        $maxInt16 = 32768.0;
        foreach ($unpacked as $val) {
            $normalizedData[] = $val / $maxInt16;
        }

        return [
            'rate' => $sampleRate,
            'data' => np::array($normalizedData)
        ];
    }
}
