#!/usr/bin/env node
// Node test runner for UniConnect API (requires Node >= 18/20)
// Usage: BACKEND_URL=http://localhost:8000 TOKEN=... node scripts/run_tests.js tts "Hola"

import fs from 'fs'
import path from 'path'

const BASE = process.env.BACKEND_URL || 'http://localhost:8000'
const TOKEN = process.env.TOKEN || ''

async function tts(text) {
  console.log('Calling text-to-speech with:', text)
  const body = new URLSearchParams()
  body.append('text', text)
  body.append('voice', 'male')
  body.append('language', 'es')

  const res = await fetch(`${BASE}/api/v1/audio/text-to-speech`, {
    method: 'POST',
    headers: TOKEN ? { 'Authorization': `Bearer ${TOKEN}` } : undefined,
    body: body,
  })
  const data = await res.json().catch(() => null)
  console.log('Status:', res.status)
  console.log(data)
}

async function stt(file) {
  if (!fs.existsSync(file)) { console.error('File not found', file); process.exit(1) }
  console.log('Calling speech-to-text with file:', file)
  const form = new FormData()
  form.append('audio_file', fs.createReadStream(path.resolve(file)))
  const res = await fetch(`${BASE}/api/v1/audio/speech-to-text`, {
    method: 'POST',
    headers: TOKEN ? { 'Authorization': `Bearer ${TOKEN}` } : undefined,
    body: form,
  })
  const data = await res.json().catch(() => null)
  console.log('Status:', res.status)
  console.log(data)
}

async function uploadSign(file) {
  if (!fs.existsSync(file)) { console.error('File not found', file); process.exit(1) }
  console.log('Uploading sign-language video:', file)
  const form = new FormData()
  form.append('file', fs.createReadStream(path.resolve(file)))
  form.append('title', 'Automated capture')
  form.append('category', 'custom')
  form.append('difficulty_level', 'beginner')
  form.append('region', 'colombian')
  form.append('language', 'es-CO')

  const res = await fetch(`${BASE}/api/v1/sign-languages`, {
    method: 'POST',
    headers: TOKEN ? { 'Authorization': `Bearer ${TOKEN}` } : undefined,
    body: form,
  })
  const data = await res.json().catch(() => null)
  console.log('Status:', res.status)
  console.log(data)
}

async function pollSign(id, timeout = 45000) {
  console.log('Polling for transcript of id:', id)
  const start = Date.now()
  while (Date.now() - start < timeout) {
    try {
      const res = await fetch(`${BASE}/api/v1/sign-languages/${id}`)
      if (res.ok) {
        const data = await res.json().catch(() => null)
        const transcript = data?.transcript ?? data?.data?.transcript ?? null
        if (transcript) { console.log('Transcript:', transcript); return }
      }
    } catch (e) { /* ignore */ }
    console.log('Not ready yet...')
    await new Promise(r => setTimeout(r, 2000))
  }
  console.log('Transcript not available within timeout')
}

async function main() {
  const [,, cmd, arg] = process.argv
  if (!cmd) { console.error('Usage: tts|stt|upload-sign|poll-sign'); process.exit(1) }
  switch (cmd) {
    case 'tts': await tts(arg || 'Prueba de TTS'); break
    case 'stt': await stt(arg); break
    case 'upload-sign': await uploadSign(arg); break
    case 'poll-sign': await pollSign(arg); break
    default: console.error('Unknown command'); process.exit(1)
  }
}

main().catch(e => { console.error(e); process.exit(1) })
