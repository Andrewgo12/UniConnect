param(
  [string]$cmd,
  [string]$arg
)
$BASE = $env:BACKEND_URL
if (-not $BASE) { $BASE = 'http://localhost:8000' }
$TOKEN = $env:TOKEN

function Call-TTS($text) {
  Write-Host "Calling text-to-speech with: $text"
  $resp = curl -s -X POST "$BASE/api/v1/audio/text-to-speech" -H "Authorization: Bearer $TOKEN" -F "text=$text" -F "voice=male" -F "language=es"
  $resp
}

function Call-STT($file) {
  if (-not (Test-Path $file)) { Write-Host "File not found: $file"; exit 1 }
  Write-Host "Calling speech-to-text with file: $file"
  $resp = curl -s -X POST "$BASE/api/v1/audio/speech-to-text" -H "Authorization: Bearer $TOKEN" -F "audio_file=@$file"
  $resp
}

function Upload-Sign($file) {
  if (-not (Test-Path $file)) { Write-Host "File not found: $file"; exit 1 }
  Write-Host "Uploading sign-language video: $file"
  $resp = curl -s -X POST "$BASE/api/v1/sign-languages" -H "Authorization: Bearer $TOKEN" -F "file=@$file" -F "title=Automated capture" -F "category=custom" -F "difficulty_level=beginner" -F "region=colombian" -F "language=es-CO"
  $resp
}

function Poll-Sign($id) {
  Write-Host "Polling sign-language resource $id for transcript"
  $end = (Get-Date).AddSeconds(45)
  while ((Get-Date) -lt $end) {
    try {
      $resp = curl -s "$BASE/api/v1/sign-languages/$id" | ConvertFrom-Json
      $transcript = $resp.transcript
      if (-not $transcript) { $transcript = $resp.data.transcript }
      if ($transcript) { Write-Host "Transcript:"; Write-Host $transcript; return }
    } catch { }
    Write-Host "Not ready yet..."
    Start-Sleep -Seconds 2
  }
  Write-Host "Transcript not available within timeout"
}

switch ($cmd) {
  'tts' { Call-TTS $arg }
  'stt' { Call-STT $arg }
  'upload-sign' { Upload-Sign $arg }
  'poll-sign' { Poll-Sign $arg }
  default { Write-Host "Unknown command. Use tts|stt|upload-sign|poll-sign" }
}
