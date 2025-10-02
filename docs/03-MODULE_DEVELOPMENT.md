# Modul-utvikling for Nubilux

## 🎯 Introduksjon

Denne guiden viser hvordan du kan lage moduler for Nubilux-systemet. Moduler lar deg utvide funksjonaliteten uten å endre kjernesystemet.

## 📁 Modul-struktur

Hver modul må ligge i sin egen mappe under `modules/`:

```
modules/
└── weather/                    # Modulnavn
    ├── WeatherController.php   # Hovedkontroller
    ├── WeatherModel.php        # Datamodell (valgfri)
    ├── bootstrap.php           # Initialisering (valgfri)
    ├── config.php              # Konfigurasjon
    ├── views/                  # Templates
    │   ├── index.php           # Hovedvisning
    │   └── widget.php          # Widget-visning
    ├── assets/                 # Modul-spesifikke ressurser
    │   ├── weather.css
    │   └── weather.js
    └── README.md              # Dokumentasjon
```

## 🚀 Lag din første modul

### Steg 1: Opprett mappestruktur

```php
// Eksempel: Værmodul
mkdir modules/weather
mkdir modules/weather/views
mkdir modules/weather/assets
```

### Steg 2: Lag Controller

**modules/weather/WeatherController.php**
```php
<?php

class WeatherController extends Controller {
    private $weatherModel;
    
    public function __construct() {
        parent::__construct();
        require_once __DIR__ . '/WeatherModel.php';
        $this->weatherModel = new WeatherModel();
    }
    
    public function index() {
        $weather = $this->weatherModel->getCurrentWeather();
        
        return $this->render('weather/views/index', [
            'weather' => $weather,
            'title' => 'Værmelding'
        ]);
    }
    
    public function widget() {
        $weather = $this->weatherModel->getCurrentWeather();
        
        // Return only the widget HTML (for AJAX loading)
        include __DIR__ . '/views/widget.php';
    }
    
    public function api() {
        $weather = $this->weatherModel->getCurrentWeather();
        
        return $this->json([
            'success' => true,
            'data' => $weather
        ]);
    }
    
    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings = [
                'api_key' => $_POST['api_key'] ?? '',
                'location' => $_POST['location'] ?? 'Oslo',
                'units' => $_POST['units'] ?? 'metric'
            ];
            
            $this->weatherModel->saveSettings($settings);
            
            return $this->json([
                'success' => true,
                'message' => 'Innstillinger lagret'
            ]);
        }
        
        $settings = $this->weatherModel->getSettings();
        return $this->render('weather/views/settings', [
            'settings' => $settings,
            'title' => 'Vær-innstillinger'
        ]);
    }
}
```

### Steg 3: Lag Model (valgfri)

**modules/weather/WeatherModel.php**
```php
<?php

class WeatherModel extends Model {
    protected $table = 'user_modules';
    
    public function getCurrentWeather() {
        $settings = $this->getSettings();
        
        if (empty($settings['api_key'])) {
            return ['error' => 'API-nøkkel mangler'];
        }
        
        $url = "https://api.openweathermap.org/data/2.5/weather";
        $params = [
            'q' => $settings['location'],
            'appid' => $settings['api_key'],
            'units' => $settings['units'],
            'lang' => 'no'
        ];
        
        $response = file_get_contents($url . '?' . http_build_query($params));
        $data = json_decode($response, true);
        
        if (!$data || isset($data['error'])) {
            return ['error' => 'Kunne ikke hente værdata'];
        }
        
        return [
            'location' => $data['name'],
            'temperature' => round($data['main']['temp']),
            'description' => $data['weather'][0]['description'],
            'icon' => $data['weather'][0]['icon'],
            'humidity' => $data['main']['humidity'],
            'wind_speed' => $data['wind']['speed']
        ];
    }
    
    public function getSettings() {
        $userId = Auth::id();
        if (!$userId) return [];
        
        $result = $this->findBy('user_id', $userId);
        if ($result && $result['module_name'] === 'weather') {
            return json_decode($result['settings'], true) ?: [];
        }
        
        // Default settings
        return [
            'api_key' => '',
            'location' => 'Oslo',
            'units' => 'metric'
        ];
    }
    
    public function saveSettings($settings) {
        $userId = Auth::id();
        if (!$userId) return false;
        
        $existing = $this->query(
            "SELECT id FROM user_modules WHERE user_id = ? AND module_name = 'weather'",
            [$userId]
        );
        
        if ($existing) {
            return $this->update($existing[0]['id'], [
                'settings' => json_encode($settings),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->create([
                'user_id' => $userId,
                'module_name' => 'weather',
                'settings' => json_encode($settings),
                'enabled' => 1
            ]);
        }
    }
}
```

### Steg 4: Lag Views

**modules/weather/views/index.php**
```php
<div class="weather-module">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-cloud-sun"></i> Værmelding</h3>
        </div>
        <div class="card-body">
            <?php if (isset($weather['error'])): ?>
                <div class="alert alert-warning">
                    <?= htmlspecialchars($weather['error']) ?>
                    <a href="/weather/settings" class="btn btn-sm btn-primary ms-2">Konfigurer</a>
                </div>
            <?php else: ?>
                <div class="weather-display">
                    <div class="weather-main">
                        <img src="https://openweathermap.org/img/w/<?= $weather['icon'] ?>.png" 
                             alt="<?= htmlspecialchars($weather['description']) ?>">
                        <span class="temperature"><?= $weather['temperature'] ?>°</span>
                    </div>
                    <div class="weather-details">
                        <h4><?= htmlspecialchars($weather['location']) ?></h4>
                        <p><?= htmlspecialchars($weather['description']) ?></p>
                        <small>
                            Luftfuktighet: <?= $weather['humidity'] ?>% | 
                            Vind: <?= $weather['wind_speed'] ?> m/s
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
```

**modules/weather/views/widget.php**
```php
<div class="weather-widget">
    <?php if (isset($weather['error'])): ?>
        <div class="widget-error">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Vær ikke tilgjengelig</span>
        </div>
    <?php else: ?>
        <div class="widget-content">
            <img src="https://openweathermap.org/img/w/<?= $weather['icon'] ?>.png" 
                 alt="<?= htmlspecialchars($weather['description']) ?>">
            <span class="temp"><?= $weather['temperature'] ?>°</span>
            <span class="location"><?= htmlspecialchars($weather['location']) ?></span>
        </div>
    <?php endif; ?>
</div>
```

### Steg 5: Lag CSS og JavaScript

**modules/weather/assets/weather.css**
```css
.weather-module .weather-display {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.weather-module .weather-main {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.weather-module .temperature {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--primary-color);
}

.weather-widget {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
}

.weather-widget .temp {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-color);
}

.weather-widget .location {
    font-size: 0.875rem;
    color: var(--secondary-color);
}
```

**modules/weather/assets/weather.js**
```javascript
// Weather module JavaScript
window.WeatherModule = {
    init: function() {
        this.setupAutoRefresh();
        this.setupSettingsForm();
    },
    
    setupAutoRefresh: function() {
        setInterval(() => {
            this.refreshWidget();
        }, 300000); // Refresh every 5 minutes
    },
    
    refreshWidget: function() {
        fetch('/api/weather')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update widget content
                    this.updateWidget(data.data);
                }
            })
            .catch(error => {
                console.error('Weather refresh failed:', error);
            });
    },
    
    updateWidget: function(weather) {
        const widget = document.querySelector('.weather-widget');
        if (widget && !weather.error) {
            widget.innerHTML = `
                <div class="widget-content">
                    <img src="https://openweathermap.org/img/w/${weather.icon}.png" 
                         alt="${weather.description}">
                    <span class="temp">${weather.temperature}°</span>
                    <span class="location">${weather.location}</span>
                </div>
            `;
        }
    },
    
    setupSettingsForm: function() {
        const form = document.querySelector('#weather-settings-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSettings(form);
            });
        }
    },
    
    saveSettings: function(form) {
        const formData = new FormData(form);
        
        fetch('/weather/settings', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Nubilux.showAlert('success', data.message);
                // Refresh weather data
                this.refreshWidget();
            } else {
                Nubilux.showAlert('danger', data.message);
            }
        })
        .catch(error => {
            Nubilux.showAlert('danger', 'En feil oppstod');
            console.error('Settings save failed:', error);
        });
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    WeatherModule.init();
});
```

### Steg 6: Registrer modulen

**config/modules.php** (legg til):
```php
'weather' => [
    'enabled' => true,
    'path' => 'modules/weather',
    'routes' => [
        '/weather' => 'WeatherController@index',
        '/weather/widget' => 'WeatherController@widget',
        '/weather/settings' => 'WeatherController@settings',
        '/api/weather' => 'WeatherController@api'
    ],
    'dependencies' => [],
    'version' => '1.0.0',
    'assets' => [
        'css' => ['/modules/weather/assets/weather.css'],
        'js' => ['/modules/weather/assets/weather.js']
    ]
]
```

## 📋 Best Practices

### **1. Navngivning**
- Bruk beskrivende navn: `WeatherController`, ikke `WC`
- Følg PHP naming conventions
- Modul-navn i lowercase med understrek

### **2. Feilhåndtering**
```php
try {
    $data = $this->apiCall();
    return $this->json(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    error_log("Weather module error: " . $e->getMessage());
    return $this->json(['success' => false, 'message' => 'En feil oppstod']);
}
```

### **3. Sikkerhet**
- Valider all input
- Bruk prepared statements
- Sjekk tilganger før data-operasjoner
- Sanitiser output

### **4. Performance**
- Cache API-kall
- Optimaliser database-queries
- Lazy-load ressurser
- Minifiser CSS/JS

### **5. Konfigurasjon**
```php
// modules/weather/config.php
return [
    'name' => 'Weather',
    'version' => '1.0.0',
    'description' => 'Værmelding og værvarsler',
    'author' => 'Ditt navn',
    'dependencies' => [],
    'settings' => [
        'api_key' => [
            'type' => 'text',
            'label' => 'API-nøkkel',
            'required' => true
        ],
        'location' => [
            'type' => 'text',
            'label' => 'Sted',
            'default' => 'Oslo'
        ]
    ]
];
```

## 🔧 Testing

### **Unit testing**
```php
// tests/modules/WeatherTest.php
class WeatherTest extends PHPUnit\Framework\TestCase {
    public function testWeatherData() {
        $weather = new WeatherModel();
        $data = $weather->getCurrentWeather();
        
        $this->assertArrayHasKey('temperature', $data);
        $this->assertIsNumeric($data['temperature']);
    }
}
```

### **Integration testing**
```bash
# Test modul-lasting
curl http://localhost/weather
curl http://localhost/api/weather
```

## 📦 Publisering

Når modulen er klar, kan den deles via:
1. GitHub repository
2. ZIP-fil
3. Nubilux module registry (fremtidig)

Inkluder alltid:
- README.md med installasjonsinstruksjoner
- Versjonsnummer
- Avhengigheter
- Lisens-informasjon