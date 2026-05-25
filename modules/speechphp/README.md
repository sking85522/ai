# 🎵 SpeechPHP — Audio Processing Library

> **Python Equivalent:** Librosa / scipy.io.wavfile
> **Purpose:** Audio I/O, MFCC extraction, Spectrogram, Pitch detection

---

## Audio I/O

### `read(filepath)` — Read WAV file
```php
$audio = SpeechPHP::read('voice.wav');
// ['rate' => 16000, 'data' => NDArray of amplitudes]
```

### `write(filepath, sampleRate, data)` — Write WAV file
```php
SpeechPHP::write('output.wav', 44100, $audioData);
```

### `generate_tone(frequency, duration, sampleRate)` — Generate sine wave
```php
$tone = SpeechPHP::generate_tone(440, 2.0, 44100);
// 440 Hz (A4 note), 2 seconds, 44100 Hz sample rate
SpeechPHP::write('beep.wav', 44100, $tone);
```

---

## Basic Features

### `zero_crossing_rate(data)` — ZCR
Measures how often the signal crosses zero. High ZCR = noise/unvoiced speech.
```php
$zcr = SpeechPHP::zero_crossing_rate($audio['data']);
// 0.15 (15% of samples cross zero)
```

### `rms_energy(data)` — Loudness
Root Mean Square energy — represents volume.
```php
$energy = SpeechPHP::rms_energy($audio['data']);
// Higher value = louder audio
```

---

## MFCC — Mel-Frequency Cepstral Coefficients

The **#1 feature** used in all speech recognition systems. Compresses audio into 13 coefficients per frame.

### `mfcc(signal, sampleRate, numCoeffs)` — Extract MFCCs
```php
$mfccs = SpeechPHP::mfcc($audio['data'], 16000, 13);
// Returns 2D array: [numFrames][13 coefficients]
// Each row = one time frame, each column = one MFCC coefficient
```

**Pipeline:** Raw Audio → Hamming Window → FFT → Mel Filter Bank → Log → DCT → 13 MFCCs

### `MFCCExtractor(sampleRate, numCoeffs, frameSize)` — Custom config
```php
$extractor = SpeechPHP::MFCCExtractor(8000, 20, 256);
$mfccs = $extractor->compute($signal);
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `sampleRate` | 16000 | Audio sample rate in Hz |
| `numCoeffs` | 13 | Number of MFCC coefficients |
| `frameSize` | 512 | FFT frame size |
| `hopSize` | 256 | Samples between frames |
| `numFilters` | 26 | Number of Mel filter banks |

---

## Spectrogram

### `spectrogram(signal, frameSize, hopSize, sampleRate)`
Time-frequency representation of audio.
```php
$spec = SpeechPHP::spectrogram($audio['data'], 512, 256, 16000);
// [
//   'data' => 2D [time_frames][freq_bins],
//   'timeAxis' => [0.0, 0.016, 0.032, ...],
//   'freqAxis' => [0, 31.25, 62.5, ...]
// ]
```

### `spectrogram_db(data)` — Convert to decibels
```php
$dbSpec = SpeechPHP::spectrogram_db($spec['data']);
// Values now in dB scale
```

---

## Pitch Detection

### `detect_pitch(signal, sampleRate)` — Single frame
```php
$pitch = SpeechPHP::detect_pitch($audio['data'], 16000);
// [
//   'frequency' => 261.63,    ← Hz (Middle C)
//   'period' => 61,           ← samples
//   'confidence' => 0.8742    ← 0 to 1
// ]
```

### `pitch_track(signal, sampleRate, frameSize)` — Multi-frame tracking
```php
$pitches = SpeechPHP::pitch_track($audio['data'], 16000, 1024);
// Array of pitch results for each time frame
foreach ($pitches as $frame) {
    echo "Time: {$frame['frequency']} Hz\n";
}
```

---

## Full Example — Voice Analysis
```php
use SpeechPHP\SpeechPHP as sp;

$audio = sp::read('speech.wav');
echo "Sample Rate: {$audio['rate']} Hz\n";
echo "Loudness (RMS): " . sp::rms_energy($audio['data']) . "\n";
echo "ZCR: " . sp::zero_crossing_rate($audio['data']) . "\n";

$pitch = sp::detect_pitch($audio['data'], $audio['rate']);
echo "Pitch: {$pitch['frequency']} Hz (Confidence: {$pitch['confidence']})\n";

$mfccs = sp::mfcc($audio['data'], $audio['rate']);
echo "MFCC Frames: " . count($mfccs) . ", Coefficients per frame: " . count($mfccs[0]) . "\n";
```
