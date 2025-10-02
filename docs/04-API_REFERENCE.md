# API Referanse - Nubilux v2.0

## 🌐 Base URL
```
https://nubilux.com/api/v1
```

## 🔐 Autentisering

### API Tokens
```http
Authorization: Bearer {api_token}
```

### Session-basert (for web-interface)
```http
X-CSRF-Token: {csrf_token}
```

## 📊 Standard Response Format

### Success Response
```json
{
    "success": true,
    "data": {...},
    "message": "Operation completed successfully"
}
```

### Error Response
```json
{
    "success": false,
    "error": "Error message",
    "code": "ERROR_CODE",
    "details": {...}
}
```

## 👤 User Endpoints

### GET /user
Hent brukerinformasjon
```http
GET /api/v1/user
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "email": "user@example.com",
        "created_at": "2024-01-01T00:00:00Z",
        "is_verified": true,
        "profile_image": null,
        "timezone": "Europe/Oslo",
        "language": "no"
    }
}
```

### POST /user/update
Oppdater brukerinformasjon
```http
POST /api/v1/user/update
Content-Type: application/json

{
    "timezone": "Europe/Oslo",
    "language": "no",
    "theme": "dark"
}
```

### POST /user/change-password
Endre passord
```http
POST /api/v1/user/change-password
Content-Type: application/json

{
    "current_password": "old_password",
    "new_password": "new_password",
    "confirm_password": "new_password"
}
```

## 🏠 Household Endpoints

### GET /households
Hent brukerens husholdninger
```http
GET /api/v1/households
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Min Familie",
            "role": "owner",
            "member_count": 3,
            "created_at": "2024-01-01T00:00:00Z"
        }
    ]
}
```

### POST /households
Opprett ny husholdning
```http
POST /api/v1/households
Content-Type: application/json

{
    "name": "Ny Husholdning"
}
```

### GET /households/{id}/members
Hent medlemmer i husholdning
```http
GET /api/v1/households/1/members
```

### POST /households/{id}/invite
Inviter medlem til husholdning
```http
POST /api/v1/households/1/invite
Content-Type: application/json

{
    "email": "newmember@example.com",
    "role": "member"
}
```

## 🔌 Module Endpoints

### GET /modules
Hent tilgjengelige moduler
```http
GET /api/v1/modules
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "name": "weather",
            "version": "1.0.0",
            "enabled": true,
            "description": "Værmelding og værvarsler",
            "author": "Nubilux",
            "settings": {...}
        }
    ]
}
```

### POST /modules/{name}/enable
Aktiver modul
```http
POST /api/v1/modules/weather/enable
```

### POST /modules/{name}/disable
Deaktiver modul
```http
POST /api/v1/modules/weather/disable
```

### GET /modules/{name}/settings
Hent modul-innstillinger
```http
GET /api/v1/modules/weather/settings
```

### POST /modules/{name}/settings
Oppdater modul-innstillinger
```http
POST /api/v1/modules/weather/settings
Content-Type: application/json

{
    "api_key": "your_api_key",
    "location": "Oslo",
    "units": "metric"
}
```

## 🌤️ Weather Module API (Eksempel)

### GET /weather/current
Hent nåværende vær
```http
GET /api/v1/weather/current
```

**Response:**
```json
{
    "success": true,
    "data": {
        "location": "Oslo",
        "temperature": 15,
        "description": "delvis skyet",
        "icon": "02d",
        "humidity": 65,
        "wind_speed": 3.2,
        "feels_like": 14,
        "updated_at": "2024-01-01T12:00:00Z"
    }
}
```

### GET /weather/forecast
Hent værvarsel
```http
GET /api/v1/weather/forecast?days=5
```

**Response:**
```json
{
    "success": true,
    "data": {
        "location": "Oslo",
        "forecast": [
            {
                "date": "2024-01-01",
                "temperature_min": 10,
                "temperature_max": 18,
                "description": "sol",
                "icon": "01d",
                "precipitation": 0
            }
        ]
    }
}
```

## 📊 Dashboard API

### GET /dashboard/widgets
Hent dashboard widgets
```http
GET /api/v1/dashboard/widgets
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "weather",
            "title": "Vær",
            "type": "weather",
            "position": {"x": 0, "y": 0, "w": 2, "h": 2},
            "settings": {...},
            "data": {...}
        }
    ]
}
```

### POST /dashboard/widgets
Legg til widget
```http
POST /api/v1/dashboard/widgets
Content-Type: application/json

{
    "type": "weather",
    "position": {"x": 0, "y": 0, "w": 2, "h": 2},
    "settings": {...}
}
```

### PUT /dashboard/widgets/{id}
Oppdater widget
```http
PUT /api/v1/dashboard/widgets/weather
Content-Type: application/json

{
    "position": {"x": 2, "y": 0, "w": 2, "h": 2},
    "settings": {...}
}
```

### DELETE /dashboard/widgets/{id}
Slett widget
```http
DELETE /api/v1/dashboard/widgets/weather
```

## 🔍 Search API

### GET /search
Søk i innhold
```http
GET /api/v1/search?q=weather&type=modules&limit=10
```

**Response:**
```json
{
    "success": true,
    "data": {
        "results": [
            {
                "type": "module",
                "name": "weather",
                "title": "Værmodul",
                "description": "Værmelding og værvarsler",
                "url": "/modules/weather"
            }
        ],
        "total": 1,
        "page": 1,
        "per_page": 10
    }
}
```

## 📁 File Upload API

### POST /upload
Last opp fil
```http
POST /api/v1/upload
Content-Type: multipart/form-data

file: {binary_data}
type: "profile_image"
```

**Response:**
```json
{
    "success": true,
    "data": {
        "filename": "profile_123456.jpg",
        "url": "/storage/uploads/profile_123456.jpg",
        "size": 156789,
        "mime_type": "image/jpeg"
    }
}
```

## 📊 Analytics API

### GET /analytics/usage
Hent bruksstatistikk
```http
GET /api/v1/analytics/usage?period=30d
```

**Response:**
```json
{
    "success": true,
    "data": {
        "period": "30d",
        "page_views": 1250,
        "unique_visitors": 45,
        "active_modules": ["weather", "calendar"],
        "most_used_features": [
            {"feature": "weather", "usage": 85},
            {"feature": "dashboard", "usage": 72}
        ]
    }
}
```

## 🚨 Error Codes

| Code | Description |
|------|-------------|
| `UNAUTHORIZED` | Manglende eller ugyldig autentisering |
| `FORBIDDEN` | Ingen tilgang til ressurs |
| `NOT_FOUND` | Ressurs ikke funnet |
| `VALIDATION_ERROR` | Valideringsfeil i input |
| `MODULE_NOT_FOUND` | Modul ikke funnet |
| `MODULE_DISABLED` | Modul er deaktivert |
| `QUOTA_EXCEEDED` | Kvote overskredet |
| `RATE_LIMITED` | For mange forespørsler |
| `SERVER_ERROR` | Intern serverfeil |

## 📈 Rate Limiting

- **Standard**: 100 requests per minutt per bruker
- **API Token**: 1000 requests per minutt per token
- **Upload**: 10 filer per minutt per bruker

**Headers:**
```http
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1640995200
```

## 🔧 Development

### Testing API lokalt
```bash
# Test autentisering
curl -X POST http://localhost/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Test med token
curl -X GET http://localhost/api/v1/user \
  -H "Authorization: Bearer your_token_here"
```

### API Client eksempel
```javascript
class NubiluxAPI {
    constructor(baseUrl, token) {
        this.baseUrl = baseUrl;
        this.token = token;
    }
    
    async request(endpoint, options = {}) {
        const response = await fetch(`${this.baseUrl}${endpoint}`, {
            ...options,
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        return response.json();
    }
    
    async getUser() {
        return this.request('/user');
    }
    
    async getWeather() {
        return this.request('/weather/current');
    }
}

// Bruk
const api = new NubiluxAPI('https://nubilux.com/api/v1', 'your_token');
const user = await api.getUser();
```