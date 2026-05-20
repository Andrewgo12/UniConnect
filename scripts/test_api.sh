#!/usr/bin/env bash
# Simple test script for UniConnect API (bash)
# Usage: ./scripts/test_api.sh tts "Texto a sintetizar"

BASE=${BACKEND_URL:-"http://localhost:8000"}
TOKEN=${TOKEN:-""}
CMD=$1
ARG=$2

set -e

if [ "$CMD" = "tts" ]; then
  TEXT=${ARG:-"Prueba de TTS"}
  echo "Calling text-to-speech with text: $TEXT"
  curl -s -X POST "$BASE/api/v1/audio/text-to-speech" \
    -H "Authorization: Bearer $TOKEN" \
    -F "text=$TEXT" \
    -F "voice=male" \
    -F "language=es" \
    -o /tmp/uniconnect_tts_response.json
  echo "Response saved to /tmp/uniconnect_tts_response.json"
  cat /tmp/uniconnect_tts_response.json
elif [ "$CMD" = "stt" ]; then
  FILE=${ARG}
  if [ -z "$FILE" ]; then echo 'Provide audio file path'; exit 1; fi
  echo "Calling speech-to-text with file: $FILE"
  curl -s -X POST "$BASE/api/v1/audio/speech-to-text" \
    -H "Authorization: Bearer $TOKEN" \
    -F "audio_file=@$FILE" \
    -o /tmp/uniconnect_stt_response.json
  echo "Response saved to /tmp/uniconnect_stt_response.json"
  cat /tmp/uniconnect_stt_response.json
elif [ "$CMD" = "upload-sign" ]; then
  FILE=${ARG}
  if [ -z "$FILE" ]; then echo 'Provide video file path'; exit 1; fi
  echo "Uploading sign-language video: $FILE"
  curl -s -X POST "$BASE/api/v1/sign-languages" \
    -H "Authorization: Bearer $TOKEN" \
    -F "file=@$FILE" \
    -F "title=Automated capture" \
    -F "category=custom" \
    -F "difficulty_level=beginner" \
    -F "region=colombian" \
    -F "language=es-CO" \
    -o /tmp/uniconnect_sign_upload.json
  echo "Response saved to /tmp/uniconnect_sign_upload.json"
  cat /tmp/uniconnect_sign_upload.json
elif [ "$CMD" = "poll-sign" ]; then
  ID=${ARG}
  if [ -z "$ID" ]; then echo 'Provide sign id'; exit 1; fi
  echo "Polling sign-language resource $ID for transcript"
  END=$((SECONDS+45))
  while [ $SECONDS -lt $END ]; do
    curl -s "$BASE/api/v1/sign-languages/$ID" -o /tmp/uniconnect_sign_poll.json || true
    TRANSCRIPT=$(jq -r '.transcript // .data.transcript // empty' /tmp/uniconnect_sign_poll.json 2>/dev/null || true)
    if [ -n "$TRANSCRIPT" ]; then
      echo "Transcript found:"; echo "$TRANSCRIPT"; exit 0
    fi
    echo "Not ready yet..."
    sleep 2
  done
  echo "Transcript not available within timeout"
  cat /tmp/uniconnect_sign_poll.json
else
  echo "Unknown command. Usage: tts|stt|upload-sign|poll-sign"
  exit 2
fi
