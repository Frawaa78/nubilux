# Database Struktur - Nubilux v2.0

## 📊 Nåværende Database Schema

### **users** tabellen
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_expires TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_verification_token (verification_token),
    INDEX idx_reset_token (reset_token)
);
```

### **households** tabellen
```sql
CREATE TABLE households (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_created_by (created_by)
);
```

## 🔮 Planlagte Tabeller (v2.0)

### **modules** - Installerte moduler
```sql
CREATE TABLE modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    version VARCHAR(20) NOT NULL,
    enabled TINYINT(1) DEFAULT 1,
    config JSON,
    installed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_enabled (enabled)
);
```

### **user_modules** - Bruker-spesifikke modul-innstillinger
```sql
CREATE TABLE user_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    module_name VARCHAR(100) NOT NULL,
    settings JSON,
    enabled TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_name) REFERENCES modules(name) ON DELETE CASCADE,
    UNIQUE KEY unique_user_module (user_id, module_name),
    INDEX idx_user_id (user_id),
    INDEX idx_module_name (module_name)
);
```

### **household_members** - Husholdning-medlemmer
```sql
CREATE TABLE household_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner', 'admin', 'member') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_household_user (household_id, user_id),
    INDEX idx_household_id (household_id),
    INDEX idx_user_id (user_id)
);
```

### **permissions** - Tilgangsrettigheter
```sql
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resource_type VARCHAR(50) NOT NULL,  -- 'module', 'household', 'admin'
    resource_id INT,                     -- ID av ressurs (kan være NULL for globale tilganger)
    permission VARCHAR(50) NOT NULL,     -- 'read', 'write', 'admin', 'delete'
    granted_by INT,                      -- Hvem som ga tilgangen
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_permission (user_id, resource_type, resource_id, permission),
    INDEX idx_user_id (user_id),
    INDEX idx_resource (resource_type, resource_id)
);
```

### **activity_log** - Aktivitetslogg
```sql
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(50),
    resource_id INT,
    details JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    INDEX idx_resource (resource_type, resource_id)
);
```

### **api_tokens** - API tilgangstokens
```sql
CREATE TABLE api_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    abilities JSON,                      -- Hvilke API-kall som er tillatt
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);
```

## 🔄 Migreringsscript

### **migration_v2.sql**
```sql
-- Legg til nye kolonner i users tabellen hvis de ikke finnes
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255),
ADD COLUMN IF NOT EXISTS timezone VARCHAR(50) DEFAULT 'Europe/Oslo',
ADD COLUMN IF NOT EXISTS language VARCHAR(5) DEFAULT 'no',
ADD COLUMN IF NOT EXISTS theme VARCHAR(20) DEFAULT 'light',
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Opprett nye tabeller
-- (Her ville vi satt inn CREATE TABLE statements fra over)

-- Seed initial data
INSERT IGNORE INTO modules (name, version, enabled, config) VALUES
('weather', '1.0.0', 0, '{"api_key": "", "location": "Oslo", "units": "metric"}'),
('calendar', '1.0.0', 0, '{"default_view": "month", "start_day": 1}'),
('tasks', '1.0.0', 0, '{"default_priority": "medium", "auto_archive": true}'),
('notes', '1.0.0', 0, '{"default_format": "markdown", "auto_save": true}');
```

## 📊 Database Relationships

```
users (1) ←→ (N) household_members (N) ←→ (1) households
users (1) ←→ (N) user_modules (N) ←→ (1) modules
users (1) ←→ (N) permissions
users (1) ←→ (N) activity_log
users (1) ←→ (N) api_tokens
```

## 🔍 Vanlige Queries

### **Hent bruker med alle moduler**
```sql
SELECT u.*, 
       um.module_name, 
       um.settings as module_settings,
       m.version as module_version
FROM users u
LEFT JOIN user_modules um ON u.id = um.user_id AND um.enabled = 1
LEFT JOIN modules m ON um.module_name = m.name AND m.enabled = 1
WHERE u.id = ?;
```

### **Hent husholdning med medlemmer**
```sql
SELECT h.*, 
       u.email as member_email, 
       hm.role as member_role
FROM households h
JOIN household_members hm ON h.id = hm.household_id
JOIN users u ON hm.user_id = u.id
WHERE h.id = ?
ORDER BY hm.role, u.email;
```

### **Sjekk tilgang til modul**
```sql
SELECT COUNT(*) as has_access
FROM users u
LEFT JOIN permissions p ON u.id = p.user_id 
    AND p.resource_type = 'module' 
    AND p.resource_id = (SELECT id FROM modules WHERE name = ?)
    AND p.permission IN ('read', 'write', 'admin')
WHERE u.id = ?
   AND (p.id IS NOT NULL OR NOT EXISTS (
       SELECT 1 FROM permissions 
       WHERE user_id = u.id 
       AND resource_type = 'module' 
       AND resource_id = (SELECT id FROM modules WHERE name = ?)
   ));
```

## 🚀 Performance Optimalisering

### **Indekser**
- Primærnøkler på alle tabeller
- Fremmednøkler for relasjonell integritet
- Sammensatte indekser for vanlige queries
- JSON-indekser for konfigurasjon og innstillinger

### **Caching Strategy**
- Brukerdata: 15 minutter
- Modul-konfigurasjon: 1 time
- Tilganger: 30 minutter
- Aktivitetslogg: Ingen caching (sanntid)

### **Arkivering**
- `activity_log`: Arkiver entries eldre enn 1 år
- `api_tokens`: Slett utløpte tokens automatisk
- Regelmessig OPTIMIZE TABLE for MyISAM tabeller

## 🔧 Vedlikehold

### **Daglige oppgaver**
- Slett utløpte reset_tokens fra users
- Slett utløpte api_tokens
- Rens opp midlertidige filer i storage/

### **Ukentlige oppgaver**
- Analyser slow queries
- Sjekk database-størrelse og vekst
- Backup-validering

### **Månedlige oppgaver**
- Arkiver gammel activity_log
- Optimaliser database-tabeller
- Oppdater statistikk og indekser