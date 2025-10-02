# Nubilux Arkitektur v2.0

## 🏗️ Systemarkitektur

Nubilux er bygget med en modulær arkitektur som støtter både tradisjonell fil-basert utvikling og moderne komponent-basert utvikling.

## 📁 Mappestruktur

```
nubilux/
├── 🔧 core/                    # Kjernesystem (ikke endre)
│   ├── Router.php              # URL-routing og request håndtering
│   ├── Controller.php          # Base controller klasse
│   ├── Model.php               # Base model klasse med database-abstraksjoner
│   ├── View.php                # Template-rendering system
│   ├── Auth.php                # Autentisering og session-håndtering
│   └── ModuleLoader.php        # Plugin/modul-system
│
├── ⚙️ config/                  # Konfigurasjon
│   ├── database.php            # Database-tilkobling (eksisterende)
│   ├── app.php                 # Applikasjon-innstillinger
│   ├── modules.php             # Modul-konfigurasjon
│   └── routes.php              # URL-ruter
│
├── 🎨 shared/                  # Delte ressurser (gjenbruk)
│   ├── css/                    # Globale stilark
│   ├── js/                     # Globale JavaScript-filer
│   ├── images/                 # Bilder og ikoner
│   └── fonts/                  # Fonter
│
├── 📄 templates/               # HTML-templates (gjenbruk)
│   ├── layout/                 # Layout-filer (header, footer, base)
│   ├── components/             # Gjenbrukbare UI-komponenter
│   └── pages/                  # Side-templates
│
├── 🏠 app/                     # Hovedapplikasjon
│   ├── controllers/            # Applikasjon-kontrollere
│   ├── models/                 # Applikasjon-modeller
│   ├── services/               # Applikasjon-tjenester
│   └── views/                  # App-spesifikke views
│
├── 🔌 modules/                 # Utvidelser/moduler
│   └── [modulnavn]/            # Hver modul i egen mappe
│
├── 🌐 api/                     # API-endepunkter
│   └── v1/                     # API versjon 1
│
├── 💾 storage/                 # Lagring og cache
│   ├── uploads/                # Opplastede filer
│   ├── logs/                   # Loggfiler
│   ├── cache/                  # Cache-filer
│   └── sessions/               # Session-data
│
└── 📚 docs/                    # Dokumentasjon
    └── examples/               # Kodeeksempler
```

## 🔄 Request Flow

### 1. Traditional File-Based Routing (Eksisterende)
```
Request → index.php/login.php/register.php → Direct execution
```

### 2. Modern Route-Based System (Ny)
```
Request → index.php → Router → Controller → Model → View → Response
```

## 🎯 Design Principles

### **1. Bakoverkompatibilitet**
- Eksisterende filer (`index.php`, `login.php`, etc.) fungerer uendret
- Gradvis migrering til ny struktur
- Ingen breaking changes

### **2. Modularity**
- **Core**: Uforanderlig kjernesystem
- **App**: Din hovedapplikasjon
- **Modules**: Utvidelser som kan aktiveres/deaktiveres
- **Shared**: Gjenbrukbare ressurser

### **3. Gjenbruk**
- **Templates**: Delte layouts og komponenter
- **Assets**: Felles CSS/JS på tvers av moduler
- **Services**: Delte tjenester (email, database, etc.)

### **4. Plugin Architecture**
- Moduler kan legges til uten å endre kjernesystemet
- Hver modul er isolert i egen mappe
- Moduler registreres via konfigurasjon

## 🔧 Core Components

### **Router.php**
```php
// Håndterer URL-routing
$router->get('/dashboard', 'DashboardController@index');
$router->post('/api/login', 'AuthController@login');
```

### **Controller.php**
```php
// Base controller med felles funksjonalitet
class HomeController extends Controller {
    public function index() {
        return $this->render('home', ['title' => 'Hjem']);
    }
}
```

### **Model.php**
```php
// Base model med database-abstraksjoner
class User extends Model {
    protected $table = 'users';
    
    public function findByEmail($email) {
        return $this->findBy('email', $email);
    }
}
```

### **View.php**
```php
// Template-rendering
$view = new View();
echo $view->render('home', ['user' => $user], 'base');
```

### **ModuleLoader.php**
```php
// Plugin-system
$loader = new ModuleLoader($router);
$loader->loadModules(); // Laster alle aktiverte moduler
```

## 📊 Database Design

### **Eksisterende Tabeller**
- `users` - Brukerdata med email-verifisering
- `households` - Husholdning-data

### **Fremtidige Tabeller** (foreslått)
- `modules` - Installerte moduler
- `module_settings` - Modul-konfigurasjon
- `permissions` - Tilgangsrettigheter
- `activity_log` - Aktivitetslogg

## 🚀 Development Workflow

### **For deg som eneste utvikler:**
1. Utvikle i `app/` mappen som normalt
2. Bruk `shared/` for gjenbrukbare ressurser
3. Bruk `templates/` for gjenbrukbare HTML
4. Test moduler i `modules/` mappen

### **For fremtidige bidragsytere:**
1. Lag moduler i `modules/[modulnavn]/`
2. Følg modul-strukturen (se MODULE_DEVELOPMENT.md)
3. Registrer modul i `config/modules.php`
4. Test via API eller web-interface

## 🔒 Security Considerations

- **CSRF Protection**: Innebygd i core-systemet
- **Input Validation**: Via Controller base class
- **SQL Injection**: PDO med prepared statements
- **Session Security**: Sikker session-håndtering
- **File Uploads**: Validering og sikker lagring

## 📈 Performance

- **Caching**: File-basert cache i `storage/cache/`
- **Asset Optimization**: Minifisering av CSS/JS
- **Database**: Optimaliserte queries via Model base class
- **Lazy Loading**: Moduler lastes kun ved behov

## 🔮 Future Roadmap

1. **Phase 1**: Etabler arkitektur ✅
2. **Phase 2**: Migrer eksisterende kode
3. **Phase 3**: Implementer første modul (værdata)
4. **Phase 4**: API for eksterne moduler
5. **Phase 5**: Module marketplace/registry