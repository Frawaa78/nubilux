# Database Setup Guide

## Steg 1: Få database credentials fra One.com

1. **Logg inn på One.com kontrollpanel**
2. **Gå til:** "Databases" eller "MariaDB"
3. **Kopier disse verdiene:**
   - Database host (vanligvis `mysql.one.com`)
   - Database navn (ofte domene-basert som `nubilux_com`)
   - Username 
   - Password

## Steg 2: Oppdater database.php

1. **Åpne:** `config/database.php`
2. **Erstatt:** 
   - `DITT_DATABASE_NAVN` med ditt database navn
   - `DITT_DB_BRUKERNAVN` med ditt username
   - `DITT_DB_PASSORD` med ditt password

## Steg 3: Upload og test

```bash
# Upload database config
scp -r config/ nubilux.com@ssh.nubilux.com:/www/

# Upload database test
scp test-db.php nubilux.com@ssh.nubilux.com:/www/
```

## Steg 4: Test forbindelse

Gå til: `https://nubilux.com/test-db.php`

## Steg 5: Opprett tabeller

1. **Gå til phpMyAdmin** (fra One.com kontrollpanel)
2. **Kopier og kjør:** innholdet fra `database/setup.sql`

Eller upload `setup.sql` og kjør via SSH:
```bash
mysql -u USERNAME -p DATABASE_NAME < setup.sql
```

## Database Client for VS Code

Etter setup, koble Database Client extension til One.com MariaDB for å administrere database direkte fra VS Code!