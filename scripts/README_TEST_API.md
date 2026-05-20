Test API scripts for UniConnect

Usage (bash):

  # set backend URL and token
  export BACKEND_URL="http://localhost:8000"
  export TOKEN="YOUR_BEARER_TOKEN"

  # Speech synthesis (text → audio)
  bash ./scripts/test_api.sh tts "Hola desde la prueba"

  # Speech recognition (audio → text)
  bash ./scripts/test_api.sh stt ./samples/test_audio.wav

  # Upload sign-language video (camera → upload)
  bash ./scripts/test_api.sh upload-sign ./samples/test_sign.webm

  # Poll transcript (after upload)
  bash ./scripts/test_api.sh poll-sign SIGN_ID

Usage (PowerShell):

  $env:BACKEND_URL = 'http://localhost:8000'
  $env:TOKEN = 'YOUR_BEARER_TOKEN'
  .\scripts\test_api.ps1 tts "Hola desde la prueba"

Notes:
- The scripts use environment variables `BACKEND_URL` and `TOKEN`.
- Upload endpoints require an authenticated user token.
- Ensure the backend Laravel server is running and accessible at `BACKEND_URL`.
- The `upload-sign` command posts the video to `/api/v1/sign-languages` and returns the created resource JSON.
- The `poll-sign` command GETs `/api/v1/sign-languages/{id}` repeatedly until `transcript` is present or timeout.
