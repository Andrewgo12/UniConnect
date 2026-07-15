// Node 20+ script to test /api/v1/auth/register with various payloads
const BASE = process.env.API_BASE || 'http://localhost:8000'
const endpoint = `${BASE}/api/v1/auth/register`

const now = Date.now()

const payloads = [
  {
    name: `Valid User ${now}`,
    email: `testuser+${now}@example.com`,
    password: 'Password123!',
    password_confirmation: 'Password123!',
    terms_accepted: true,
    privacy_accepted: true,
  },
  {
    name: 'Existing Email',
    email: 'kevinrlinze@gmail.com',
    password: 'Password123!',
    password_confirmation: 'Password123!',
    terms_accepted: true,
    privacy_accepted: true,
  },
  {
    name: 'Short Password',
    email: `shortpw+${now}@example.com`,
    password: '123',
    password_confirmation: '123',
    terms_accepted: true,
    privacy_accepted: true,
  },
  {
    name: 'Numeric Password',
    email: `numericpw+${now}@example.com`,
    password: '12345678',
    password_confirmation: '12345678',
    terms_accepted: true,
    privacy_accepted: true,
  },
  {
    name: 'Letters Only Password',
    email: `letters+${now}@example.com`,
    password: 'abcdefgh',
    password_confirmation: 'abcdefgh',
    terms_accepted: true,
    privacy_accepted: true,
  },
  {
    name: 'Missing Terms',
    email: `noterms+${now}@example.com`,
    password: 'Password123!',
    password_confirmation: 'Password123!',
    // terms_accepted missing
    privacy_accepted: true,
  },
  {
    name: 'Terms False',
    email: `termsfalse+${now}@example.com`,
    password: 'Password123!',
    password_confirmation: 'Password123!',
    terms_accepted: false,
    privacy_accepted: true,
  },
  {
    name: 'Password Mismatch',
    email: `mismatch+${now}@example.com`,
    password: 'Password123!',
    password_confirmation: 'Password1234!',
    terms_accepted: true,
    privacy_accepted: true,
  },
  {
    name: 'Name Numbers Only',
    email: `namenums+${now}@example.com`,
    password: 'Password123!',
    password_confirmation: 'Password123!',
    terms_accepted: true,
    privacy_accepted: true,
  }
]

async function run() {
  for (const p of payloads) {
    const label = p.name
    const body = JSON.stringify(p)
    console.log('\n---')
    console.log('Test:', label)
    console.log('Payload:', body)
    try {
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body,
      })
      const text = await res.text()
      console.log('Status:', res.status)
      try { console.log('Response JSON:', JSON.parse(text)) } catch { console.log('Response Text:', text) }
    } catch (e) {
      console.error('Request failed:', e.message || e)
    }
  }
}

run()
  .then(() => console.log('\nDone'))
  .catch(e => { console.error('Fatal error', e); process.exit(1) })
