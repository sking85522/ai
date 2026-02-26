<?php
class CorpusShardManager
{
    public const MAX_SHARD_BYTES = 524288; // 512 KB

    public function splitTextFile(string $inputFile, string $outputDir, int $maxShardBytes = self::MAX_SHARD_BYTES): array
    {
        if (!is_file($inputFile)) {
            return ['ok' => false, 'message' => 'Input file not found', 'shards' => 0, 'bytes' => 0];
        }

        if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
            return ['ok' => false, 'message' => 'Could not create output directory', 'shards' => 0, 'bytes' => 0];
        }

        $in = fopen($inputFile, 'rb');
        if (!$in) {
            return ['ok' => false, 'message' => 'Could not open input file', 'shards' => 0, 'bytes' => 0];
        }

        $writtenBytes = 0;
        $shardCount = 0;
        $currentBytes = 0;
        $out = null;

        try {
            while (!feof($in)) {
                $line = fgets($in);
                if ($line === false) {
                    break;
                }
                if ($line === '') {
                    continue;
                }

                if ($out === null || ($currentBytes + strlen($line)) > $maxShardBytes) {
                    if (is_resource($out)) {
                        fclose($out);
                    }
                    $shardCount++;
                    $currentBytes = 0;
                    $file = $outputDir . DIRECTORY_SEPARATOR . sprintf('shard_%06d.txt', $shardCount);
                    $out = fopen($file, 'wb');
                    if (!$out) {
                        throw new RuntimeException('Could not create shard file: ' . $file);
                    }
                }

                if (strlen($line) > $maxShardBytes) {
                    $line = rtrim(substr($line, 0, $maxShardBytes - 2), "\r\n") . PHP_EOL;
                }

                fwrite($out, $line);
                $bytes = strlen($line);
                $currentBytes += $bytes;
                $writtenBytes += $bytes;
            }
        } catch (Throwable $e) {
            if (is_resource($out)) {
                fclose($out);
            }
            fclose($in);
            return ['ok' => false, 'message' => $e->getMessage(), 'shards' => $shardCount, 'bytes' => $writtenBytes];
        }

        if (is_resource($out)) {
            fclose($out);
        }
        fclose($in);

        return ['ok' => true, 'message' => 'Shards generated', 'shards' => $shardCount, 'bytes' => $writtenBytes];
    }

    public function listShardFiles(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }
        $files = glob($dir . DIRECTORY_SEPARATOR . '*.txt') ?: [];
        sort($files, SORT_NATURAL);
        return $files;
    }
}
