$targetSize = 30MB
$files = @(
  "storage/training/json_pack/patterns.sql",
  "storage/training/json_pack/training_data.sql"
)

foreach ($file in $files) {
  if (-not (Test-Path $file)) { continue }

  $dir = Split-Path $file -Parent
  $base = [System.IO.Path]::GetFileNameWithoutExtension($file)
  $ext = [System.IO.Path]::GetExtension($file)

  Get-ChildItem -Path $dir -Filter ($base + "_part_*" + $ext) -ErrorAction SilentlyContinue | Remove-Item -Force

  $reader = [System.IO.File]::OpenText($file)
  try {
    $part = 1
    $writer = $null
    $currentSize = 0

    while (($line = $reader.ReadLine()) -ne $null) {
      $bytes = [System.Text.Encoding]::UTF8.GetByteCount($line + [Environment]::NewLine)

      if (($writer -eq $null) -or (($currentSize + $bytes -gt $targetSize) -and ($currentSize -gt 0))) {
        if ($writer -ne $null) { $writer.Dispose() }
        $partPath = Join-Path $dir ("{0}_part_{1:D3}{2}" -f $base, $part, $ext)
        $writer = [System.IO.StreamWriter]::new($partPath, $false, [System.Text.UTF8Encoding]::new($false))
        $currentSize = 0
        $part++
      }

      $writer.WriteLine($line)
      $currentSize += $bytes
    }

    if ($writer -ne $null) { $writer.Dispose() }
  }
  finally {
    $reader.Dispose()
  }
}

Get-ChildItem "storage/training/json_pack" -Filter "*_part_*.sql" |
  Sort-Object Name |
  Select-Object Name,Length
