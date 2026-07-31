# Deployment Guide - Enterprise SaaS Suite & Render Cloud

This guide provides step-by-step instructions to deploy the complete multi-tenant intrusion detection system (IDS) suite. The system consists of:
1. **Python Inference Server** (Inference API server)
2. **Local Sniffer Daemon** (Interface sniffer)
3. **Laravel Livewire Multi-tenant Dashboard** (Central Portal)
4. **NPM CLI Packet Scanner** (`unesco-mine-sec-cli`)

---

## System Requirements
- **OS**: Windows, macOS, or Linux (Raspberry Pi OS supported)
- **Node.js**: v20.19+ or v22.12+
- **PHP**: v8.2+ (XAMPP recommended for Windows)
- **Composer**: Dependency Manager for PHP
- **Python**: v3.11+
- **Database**: PostgreSQL (Production) / SQLite (Local dev testing)

---

## local Developer Quickstart (SQLite)

1. Navigate to the `dashboard/` directory:
   ```bash
   cd dashboard
   ```
2. Copy the environment file:
   ```bash
   copy .env.example .env
   ```
3. Install dependencies:
   ```bash
   C:\xampp\php\php.exe C:\xampp\php\composer.phar install --no-dev --no-scripts --no-interaction
   ```
4. Create the SQLite database file:
   ```bash
   powershell -Command "New-Item -ItemType File -Path 'database/database.sqlite' -Force"
   ```
5. Run migrations to seed the SaaS schema:
   ```bash
   C:\xampp\php\php.exe artisan migrate:fresh --force
   ```
6. Setup storage folders:
   ```bash
   powershell -Command "New-Item -ItemType Directory -Path 'storage/framework/cache/data', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs' -Force"
   ```
7. Generate the application key:
   ```bash
   C:\xampp\php\php.exe artisan key:generate
   ```
8. Build Vite assets:
   ```bash
   npm install
   npm run build
   ```
9. Start local servers:
   - **Model Server**: `python src/api_service.py`
   - **Sniffer Daemon**: `python src/sniffer_daemon.py`
   - **Web Server**: `C:\xampp\php\php.exe artisan serve --port=8000`

---

## Render Cloud Production Deployment

We use Render Blueprints (`render.yaml`) to deploy the system to production with a PostgreSQL database cluster.

### 1. Project Repository Preparation
Ensure your repository contains the `render.yaml` configuration file at the root.

### 2. Deployment Steps
1. Log in to your **Render.com** account.
2. Go to **Blueprints** and click **New Blueprint Instance**.
3. Link your GitHub repository.
4. Render will read `render.yaml` and provision:
   - A PostgreSQL Database (`minesec-db`).
   - A Private Python Web Service (`api-service`) exposing the CNN-LSTM model.
   - A Public PHP Laravel Web Service (`minesec-dashboard`).
5. Render will automatically migrate the database and build Vite assets on launch.

---

## Obtaining API & Organization Tokens

### 1. Registering your Organization
1. Open the dashboard (e.g. `http://localhost:8000/signup`).
2. Register your Organization Name and create an Administrator user profile.

### 2. Generating Device API Keys
1. Go to the **Device Nodes** tab inside the dashboard.
2. Enter a name for your network node (e.g., *Mine Shaft 4 Sniffer*) and click **Generate Access Key**.
3. Copy the resulting bearer token (e.g., `unesco_device_xxxxxxxx...`). **This token will only be shown once.**

### 3. Deploying the CLI Client (NPM/Yarn/pnpm)
Edge node installation:
1. Navigate to the CLI directory:
   `cd npm-packet-scanner`
2. Install dependencies & run:
   - **npm**: `npm install && node index.js`
   - **Yarn**: `yarn install && node index.js`
   - **pnpm**: `pnpm install && node index.js`
3. Global installation (Optional):
   - **npm**: `npm install -g ./`
   - **Yarn**: `yarn global add file:./`
   - **pnpm**: `pnpm add -g ./`
   Then run globally with: `unesco-mine-sec-cli`
4. Input the central dashboard URL, select the active network interface, and paste the generated Device API Key. Telemetry will begin streaming to the dashboard.
