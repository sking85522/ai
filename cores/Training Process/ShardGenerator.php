<?php
class ShardGenerator
{
    /**
     * Split a large text file into smaller shard files by number of lines.
     * Returns array of created shard file paths.
     */
    public static function splitFileByLines(string $inputPath, string $outDir, int $linesPerFile = 10000): array
    {
        if (!is_readable($inputPath)) {
            throw new RuntimeException("ShardGenerator: cannot read input file: $inputPath");
        }

        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        $handle = fopen($inputPath, 'r');
        if (!$handle) {
            throw new RuntimeException("ShardGenerator: failed opening $inputPath");
        }

        $files = [];
        $fileIndex = 0;
        $lineCount = 0;
        $outHandle = null;

        while (!feof($handle)) {
            $line = fgets($handle);
            if ($line === false) {
                break;
            }

            if ($lineCount % $linesPerFile === 0) {
                if ($outHandle) {
                    fclose($outHandle);
                }
                $fileIndex++;
                $outPath = rtrim($outDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . sprintf('shard-%05d.txt', $fileIndex);
                $files[] = $outPath;
                $outHandle = fopen($outPath, 'w');
                if (!$outHandle) {
                    fclose($handle);
                    throw new RuntimeException("ShardGenerator: cannot create shard file: $outPath");
                }
            }

            fwrite($outHandle, $line);
            $lineCount++;
        }

        if ($outHandle) {
            fclose($outHandle);
        }
        fclose($handle);

        return $files;
    }
}
