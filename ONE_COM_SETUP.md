# Nubilux - One.com Setup Guide

## Fordeler med One.com vs Azure:
- **Kostnad**: ~50-100 kr/måned vs ~300+ kr/måned
- **Enklere**: Standard web hosting uten kompleksitet
- **MariaDB**: Inkludert i hostingpakken
- **SFTP/SSH**: Standard tilgang

## One.com Setup Steg:

### 1. One.com Hosting
- **Pakke**: Web hosting med PHP og MariaDB
- **Kostnad**: ~79 kr/måned for Medium pakke
- **Inkluderer**: 
  - PHP 8.x support
  - MariaDB database
  - SFTP/SSH tilgang
  - SSL sertifikat

### 2. VS Code SFTP Setup
```json
{
    "name": "One.com SFTP",
    "host": "ssh.one.com",
    "protocol": "sftp",
    "port": 22,
    "username": "DITT_BRUKERNAVN",
    "password": "DITT_PASSORD",
    "remotePath": "/",
    "uploadOnSave": true,
    "ignore": [".vscode/**", ".git/**", "*.md"]
}
```

### 3. Database Connection fra VS Code
**Extensions å installere:**
- `MySQL` by Jun Han (for database queries)
- `Database Client` by Weijan Chen (for GUI database management)

**Connection settings:**
- Host: `mysql.one.com`
- Port: `3306`
- Database: `DITT_DOMENE_dk` (eller lignende)
- Username: Fra one.com kontrollpanel
- Password: Fra one.com kontrollpanel

### 4. PHP Database Connection
```php
<?php
$host = 'mysql.one.com';
$dbname = 'DITT_DOMENE_dk';
$username = 'DITT_DB_BRUKER';
$password = 'DITT_DB_PASSORD';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

## Workflow:
1. **Skriv kode** i VS Code lokalt
2. **Auto-upload** via SFTP til one.com
3. **Test live** på ditt domene
4. **Database management** direkte i VS Code
5. **Git backup** til GitHub

## Neste steg:
1. Bestill one.com hosting
2. Få SFTP og database credentials
3. Konfigurer VS Code extensions
4. Flytt index.php til one.com