# UniConnect API Documentation

## 📋 Table of Contents

1. [Authentication](#authentication)
2. [Users](#users)
3. [Messages](#messages)
4. [Conversations](#conversations)
5. [Sign Language](#sign-language)
6. [Audio](#audio)
7. [Images](#images)
8. [Emergencies](#emergencies)
9. [Medical Records](#medical-records)
10. [Analytics](#analytics)
11. [Security](#security)
12. [Error Codes](#error-codes)
13. [Rate Limiting](#rate-limiting)
14. [Accessibility Features](#accessibility-features)

---

## 🔐 Authentication

### POST /api/v1/auth/register
Register a new user with accessibility preferences.

**Request Body:**
```json
{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "accessibility_needs": ["blind", "deaf"],
    "terms_accepted": true,
    "privacy_accepted": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Usuario registrado exitosamente",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan@example.com",
            "accessibility_needs": ["blind", "deaf"],
            "created_at": "2024-01-01T00:00:00Z"
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_at": "2024-01-02T00:00:00Z"
    }
}
```

### POST /api/v1/auth/login
Authenticate user and return access token.

**Request Body:**
```json
{
    "email": "juan@example.com",
    "password": "password123",
    "device_type": "mobile",
    "accessibility_mode": "screen_reader"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Inicio de sesión exitoso",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_at": "2024-01-02T00:00:00Z",
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan@example.com"
        }
    }
}
```

### POST /api/v1/auth/logout
Logout user and invalidate access token.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Sesión cerrada exitosamente"
}
```

### GET /api/v1/auth/me
Get current authenticated user profile.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan@example.com",
            "accessibility_needs": ["blind", "deaf"],
            "profile": {
                "bio": "Usuario con necesidades de accesibilidad",
                "preferences": {
                    "language": "es-CO",
                    "theme": "high_contrast"
                }
            }
        }
    }
}
```

---

## 👥 Users

### GET /api/v1/users
Get list of users (admin only).

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `limit` (integer): Items per page (default: 15)
- `search` (string): Search users by name or email

**Response:**
```json
{
    "success": true,
    "data": {
        "users": [
            {
                "id": 1,
                "name": "Juan Pérez",
                "email": "juan@example.com",
                "accessibility_needs": ["blind", "deaf"],
                "created_at": "2024-01-01T00:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 5,
            "total_items": 75,
            "items_per_page": 15
        }
    }
}
```

### GET /api/v1/users/{id}
Get specific user details.

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan@example.com",
            "accessibility_needs": ["blind", "deaf"],
            "profile": {
                "bio": "Usuario con necesidades de accesibilidad",
                "phone": "+5713001234567",
                "emergency_contact": "María Pérez - 3001234567"
            }
        }
    }
}
```

### PUT /api/v1/users/{id}
Update user profile.

**Request Body:**
```json
{
    "name": "Juan Pérez Actualizado",
    "phone": "+5713001234567",
    "accessibility_needs": ["blind", "deaf", "mute"],
    "profile": {
        "bio": "Biografía actualizada",
        "preferences": {
            "language": "es-CO",
            "theme": "high_contrast",
            "font_size": "large"
        }
    }
}
```

**Response:**
```json
{
    "success": true,
    "message": "Perfil actualizado exitosamente",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez Actualizado",
            "accessibility_needs": ["blind", "deaf", "mute"]
        }
    }
}
```

---

## 💬 Messages

### GET /api/v1/messages
Get messages for authenticated user.

**Query Parameters:**
- `conversation_id` (integer): Filter by conversation
- `page` (integer): Page number
- `limit` (integer): Items per page
- `type` (string): Filter by type (text, voice, video, image, sign_language)

**Response:**
```json
{
    "success": true,
    "data": {
        "messages": [
            {
                "id": 1,
                "content": "Hola, ¿cómo estás?",
                "type": "text",
                "status": "sent",
                "user": {
                    "id": 1,
                    "name": "Juan Pérez"
                },
                "conversation": {
                    "id": 1,
                    "title": "Conversación Principal"
                },
                "created_at": "2024-01-01T10:30:00Z",
                "accessibility_data": {
                    "screen_reader_optimized": true,
                    "voice_commands": true
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 3,
            "total_items": 45,
            "items_per_page": 15
        }
    }
}
```

### POST /api/v1/messages
Send a new message.

**Request Body:**
```json
{
    "conversation_id": 1,
    "content": "Hola, ¿cómo estás?",
    "type": "text",
    "priority": "normal",
    "accessibility_data": {
        "screen_reader_optimized": true,
        "voice_commands": true
    }
}
```

**Response:**
```json
{
    "success": true,
    "message": "Mensaje enviado exitosamente",
    "data": {
        "message": {
            "id": 46,
            "content": "Hola, ¿cómo estás?",
            "type": "text",
            "status": "sent",
            "created_at": "2024-01-01T10:30:00Z"
        }
    }
}
```

### PUT /api/v1/messages/{id}
Update a message.

**Request Body:**
```json
{
    "content": "Hola, ¿cómo estás? (editado)",
    "is_edited": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Mensaje actualizado exitosamente",
    "data": {
        "message": {
            "id": 1,
            "content": "Hola, ¿cómo estás? (editado)",
            "is_edited": true,
            "edited_at": "2024-01-01T10:35:00Z"
        }
    }
}
```

---

## 🗨️ Conversations

### GET /api/v1/conversations
Get user conversations.

**Query Parameters:**
- `page` (integer): Page number
- `limit` (integer): Items per page
- `type` (string): Filter by type (individual, group, support, emergency)

**Response:**
```json
{
    "success": true,
    "data": {
        "conversations": [
            {
                "id": 1,
                "title": "Conversación Principal",
                "type": "individual",
                "status": "active",
                "created_by": 1,
                "participants": [
                    {
                        "id": 1,
                        "name": "Juan Pérez",
                        "role": "admin"
                    }
                ],
                "last_message": {
                    "content": "Hola, ¿cómo estás?",
                    "created_at": "2024-01-01T10:30:00Z"
                },
                "unread_count": 0,
                "message_count": 15,
                "created_at": "2024-01-01T00:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 2,
            "total_items": 25,
            "items_per_page": 15
        }
    }
}
```

### POST /api/v1/conversations
Create new conversation.

**Request Body:**
```json
{
    "title": "Nueva Conversación",
    "type": "group",
    "participants": [2, 3],
    "category": "general"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Conversación creada exitosamente",
    "data": {
        "conversation": {
            "id": 26,
            "title": "Nueva Conversación",
            "type": "group",
            "status": "active",
            "created_at": "2024-01-01T12:00:00Z"
        }
    }
}
```

---

## 🤟 Sign Language

### GET /api/v1/sign-language
Get sign language videos.

**Query Parameters:**
- `page` (integer): Page number
- `limit` (integer): Items per page
- `category` (string): Filter by category (basic, medical, emergency, education, custom)
- `difficulty_level` (string): Filter by difficulty (beginner, intermediate, advanced)
- `region` (string): Filter by region (colombian, international, local)

**Response:**
```json
{
    "success": true,
    "data": {
        "sign_languages": [
            {
                "id": 1,
                "title": "Hola en LSC",
                "description": "Saludo básico en lenguaje de señas colombiano",
                "category": "basic",
                "difficulty_level": "beginner",
                "region": "colombian",
                "video_url": "storage/sign_languages/video1.mp4",
                "thumbnail_url": "storage/sign_languages/thumbnails/video1.jpg",
                "duration": 120,
                "tags": ["saludo", "basico"],
                "is_public": true,
                "language": "es-CO",
                "transcript": "Gesto: mano abierta con palm hacia adelante, luego cerrar",
                "created_at": "2024-01-01T00:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 5,
            "total_items": 50,
            "items_per_page": 10
        }
    }
}
```

### POST /api/v1/sign-language
Upload new sign language video.

**Request Body (multipart/form-data):**
```
file: [video file]
title: "Hola en LSC Avanzado"
description: "Saludo avanzado con gestos complejos"
category: "basic"
difficulty_level: "intermediate"
region: "colombian"
tags: ["saludo", "avanzado", "gestos"]
is_public: true
language: "es-CO"
transcript: "Gesto: mano abierta con palm hacia adelante, rotación 180°, luego cerrar con fuerza"
```

**Response:**
```json
{
    "success": true,
    "message": "Video de lenguaje de señas subido exitosamente",
    "data": {
        "sign_language": {
            "id": 15,
            "title": "Hola en LSC Avanzado",
            "video_url": "storage/sign_languages/video15.mp4",
            "thumbnail_url": "storage/sign_languages/thumbnails/video15.jpg",
            "duration": 180,
            "created_at": "2024-01-01T14:30:00Z"
        }
    }
}
```

---

## 🎵 Audio

### GET /api/v1/audio
Get audio files.

**Query Parameters:**
- `page` (integer): Page number
- `limit` (integer): Items per page
- `type` (string): Filter by type (speech, voice_note, emergency, sign_language)
- `quality` (string): Filter by quality (low, medium, high)
- `language` (string): Filter by language

**Response:**
```json
{
    "success": true,
    "data": {
        "audios": [
            {
                "id": 1,
                "title": "Nota de Voz - Recordatorio Médico",
                "description": "Recordatorio para tomar medicación",
                "type": "voice_note",
                "file_path": "storage/audios/audio1.mp3",
                "original_name": "medicamento.mp3",
                "mime_type": "audio/mpeg",
                "size": 2048576,
                "duration": 45,
                "language": "es-CO",
                "quality": "medium",
                "transcript": "Recordatorio: tomar pastilla para presión a las 8:00 AM",
                "is_public": false,
                "created_at": "2024-01-01T09:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 3,
            "total_items": 25,
            "items_per_page": 10
        }
    }
}
```

### POST /api/v1/audio
Upload new audio file.

**Request Body (multipart/form-data):**
```
file: [audio file]
title: "Nota de Voz - Emergencia"
description: "Mensaje de emergencia grabado"
type: "emergency"
quality: "high"
language: "es-CO"
transcript: "Emergencia: necesito ayuda médica inmediata"
is_public: false
```

**Response:**
```json
{
    "success": true,
    "message": "Archivo de audio subido exitosamente",
    "data": {
        "audio": {
            "id": 8,
            "title": "Nota de Voz - Emergencia",
            "file_path": "storage/audios/emergency8.mp3",
            "duration": 30,
            "created_at": "2024-01-01T15:45:00Z"
        }
    }
}
```

---

## 🖼️ Images

### GET /api/v1/images
Get images.

**Query Parameters:**
- `page` (integer): Page number
- `limit` (integer): Items per page
- `type` (string): Filter by type (profile, sign_language, emergency, medical, general)
- `tags` (string): Filter by tags

**Response:**
```json
{
    "success": true,
    "data": {
        "images": [
            {
                "id": 1,
                "title": "Foto de Perfil",
                "description": "Mi foto de perfil actualizada",
                "type": "profile",
                "file_path": "storage/images/profile1.jpg",
                "original_name": "perfil.jpg",
                "mime_type": "image/jpeg",
                "size": 1048576,
                "width": 800,
                "height": 600,
                "alt_text": "Foto de perfil de Juan Pérez",
                "tags": ["perfil", "actualizado"],
                "is_public": false,
                "created_at": "2024-01-01T08:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 4,
            "total_items": 35,
            "items_per_page": 10
        }
    }
}
```

### POST /api/v1/images
Upload new image.

**Request Body (multipart/form-data):**
```
file: [image file]
title: "Imagen Médica"
description: "Radiografía de tórax"
type: "medical"
alt_text: "Radiografía de tórax mostrando estructura pulmonar normal"
tags: ["medica", "radiografía", "tórax"]
is_public: false
language: "es-CO"
```

**Response:**
```json
{
    "success": true,
    "message": "Archivo de imagen subido exitosamente",
    "data": {
        "image": {
            "id": 12,
            "title": "Imagen Médica",
            "file_path": "storage/images/medical12.jpg",
            "width": 1200,
            "height": 800,
            "created_at": "2024-01-01T16:20:00Z"
        }
    }
}
```

---

## 🚨 Emergencies

### GET /api/v1/emergencies
Get emergencies.

**Query Parameters:**
- `page` (integer): Page number
- `limit` (integer): Items per page
- `status` (string): Filter by status (active, resolved, acknowledged)
- `severity` (string): Filter by severity (low, medium, high, critical)
- `type` (string): Filter by type (medical, accident, violence, natural_disaster, technical, other)

**Response:**
```json
{
    "success": true,
    "data": {
        "emergencies": [
            {
                "id": 1,
                "type": "medical",
                "severity": "high",
                "description": "Dolor en el pecho y dificultad para respirar",
                "location": "Calle 123 #45-67, Bogotá",
                "latitude": 4.6097,
                "longitude": -74.0817,
                "contact_name": "María Pérez",
                "contact_phone": "+5713001234567",
                "contact_relationship": "esposa",
                "medical_conditions": ["hipertensión", "diabetes"],
                "accessibility_needs": ["blind", "deaf"],
                "status": "active",
                "created_at": "2024-01-01T20:15:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 2,
            "total_items": 15,
            "items_per_page": 10
        }
    }
}
```

### POST /api/v1/emergencies
Trigger new emergency.

**Request Body:**
```json
{
    "type": "medical",
    "severity": "critical",
    "description": "Dolor severo en el pecho, no puedo respirar",
    "location": "Calle 123 #45-67, Bogotá",
    "latitude": 4.6097,
    "longitude": -74.0817,
    "contact_name": "María Pérez",
    "contact_phone": "+5713001234567",
    "contact_relationship": "esposa",
    "medical_conditions": ["hipertensión", "diabetes", "asma"],
    "accessibility_needs": ["blind", "deaf"],
    "additional_info": "Paciente con antecedentes cardiacos"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Emergencia registrada exitosamente",
    "data": {
        "emergency": {
            "id": 25,
            "type": "medical",
            "severity": "critical",
            "status": "active",
            "created_at": "2024-01-01T21:30:00Z"
        }
    }
}
```

---

## 🏥 Medical Records

### GET /api/v1/medical-records
Get medical records.

**Query Parameters:**
- `page` (integer): Page number
- `limit` (integer): Items per page
- `type` (string): Filter by type (diagnosis, treatment, lab_result, prescription, vaccination, allergy_test)
- `user_id` (integer): Filter by user

**Response:**
```json
{
    "success": true,
    "data": {
        "medical_records": [
            {
                "id": 1,
                "title": "Diagnóstico Hipertensión",
                "type": "diagnosis",
                "diagnosis": "Hipertensión arterial primaria",
                "treatment": "Tratamiento con inhibidores de ACE y diuréticos",
                "notes": "Paciente responde bien al tratamiento",
                "created_by": 1,
                "created_at": "2024-01-01T10:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 3,
            "total_items": 25,
            "items_per_page": 10
        }
    }
}
```

---

## 📊 Analytics

### GET /api/v1/analytics
Get analytics data.

**Query Parameters:**
- `start_date` (date): Start date for analytics
- `end_date` (date): End date for analytics
- `type` (string): Filter by type (user_activity, message_analytics, emergency_stats, accessibility_usage, system_overview)
- `user_id` (integer): Filter by user

**Response:**
```json
{
    "success": true,
    "data": {
        "analytics": [
            {
                "id": 1,
                "event_type": "user_action",
                "category": "engagement",
                "action": "user_login",
                "value": 1,
                "metadata": {
                    "device_type": "mobile",
                    "platform": "android",
                    "accessibility_mode": "screen_reader"
                },
                "created_at": "2024-01-01T08:00:00Z"
            }
        ]
    }
}
```

---

## 🔒 Security

### GET /api/v1/security/logs
Get security logs (admin only).

**Response:**
```json
{
    "success": true,
    "data": {
        "security_logs": [
            {
                "id": 1,
                "event_type": "security_alert",
                "category": "authentication",
                "action": "failed_login",
                "ip_address": "192.168.1.100",
                "user_agent": "Mozilla/5.0...",
                "description": "Intento de inicio de sesión fallido",
                "severity": "medium",
                "created_at": "2024-01-01T14:30:00Z"
            }
        ]
    }
}
```

---

## ❌ Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 200 | Success | Request completed successfully |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request data |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Access denied |
| 404 | Not Found | Resource not found |
| 422 | Validation Error | Request validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

---

## ⏱️ Rate Limiting

**Limits:**
- **Authentication endpoints**: 5 requests per minute
- **File uploads**: 10 requests per hour
- **API endpoints**: 100 requests per minute
- **Emergency endpoints**: No rate limiting

**Headers:**
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1640995200
```

---

## ♿ Accessibility Features

### Vibration Patterns

The API supports vibration patterns for different accessibility needs:

**Emergency Pattern:**
- Duration: 2000ms
- Pattern: [100, 200, 100, 200, 100, 200, 100, 200]
- Intensity: High

**Message Received Pattern:**
- Duration: 500ms
- Pattern: [50, 50, 50, 50, 50, 50, 50, 50]
- Intensity: Medium

**Custom Patterns:**
Users can create custom vibration patterns with specific:
- Pattern array (timing in milliseconds)
- Duration (total duration in milliseconds)
- Intensity level (low, medium, high, maximum)

### Screen Reader Support

All text responses include:
- ARIA labels for screen readers
- Semantic HTML structure
- Alt text for images
- Proper heading hierarchy

### Voice Commands

The API supports voice commands for:
- Navigation
- Message sending
- Emergency triggering
- Accessibility mode switching

### High Contrast Mode

API responses support:
- High contrast color schemes
- Large text options
- Enhanced visual indicators
- Focus management

---

## 🔧 Development Setup

### Environment Variables

```bash
# Required
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uniconect
DB_USERNAME=root
DB_PASSWORD=password

# Optional
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
QUEUE_CONNECTION=redis
```

### Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AuthTest

# Generate coverage report
php artisan test --coverage
```

---

## 📱 Mobile App Integration

### Push Notifications

**FCM Configuration:**
```json
{
    "to": "/topics/user_123",
    "notification": {
        "title": "Nuevo mensaje",
        "body": "Tienes un nuevo mensaje en UniConnect",
        "icon": "ic_message",
        "click_action": "OPEN_MESSAGE",
        "data": {
            "message_id": 456,
            "conversation_id": 78
        }
    }
}
```

### Vibration Integration

**Android:**
```java
VibrationPattern pattern = new VibrationPattern(
    new long[]{100, 200, 100, 200, 100, 200, 100, 200},
    2000, // duration in ms
    VibrationPattern.DEFAULT_AMPLITUDE
);
```

**iOS:**
```swift
let pattern = VibrationPattern(
    timings: [100, 200, 100, 200, 100, 200, 100, 200],
    intensity: .high
)
UIImpactFeedbackGenerator.generateFeedback(pattern)
```

---

## 🚀 Deployment

### Production Environment

```bash
# Environment setup
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.uniconect.com

# Cache configuration
CACHE_DRIVER=redis
REDIS_HOST=redis-cluster.uniconect.com
REDIS_PORT=6379

# Queue configuration
QUEUE_CONNECTION=redis
QUEUE_DRIVER=redis

# Security
ENCRYPTED_KEY=base64:encrypted_key_here
```

### Docker Deployment

```dockerfile
FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev

RUN docker-php-ext-install pdo_mysql gd zip

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name api.uniconect.com;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📞 Support

### Technical Support
- **Email**: soporte@uniconect.com
- **Phone**: +571 300 123 4567
- **Documentation**: https://docs.uniconect.com
- **Status Page**: https://status.uniconect.com

### API Status
- **Current Version**: v1.0.0
- **Last Updated**: 2024-01-01
- **Uptime**: 99.9%

### Changelog
Check [CHANGELOG.md](CHANGELOG.md) for version history and updates.

---

*This documentation covers all UniConnect API endpoints with accessibility features, security considerations, and deployment guidelines.*
