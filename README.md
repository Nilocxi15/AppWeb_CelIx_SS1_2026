# CelIx - Aplicación Web (Laravel 12 + PostgreSQL 18)

Sistema de gestión para CelIx desarrollado con **Laravel 12**, **PostgreSQL 18** y soporte para desarrollo contenerizado con **Docker**.

---

## Despliegue con Docker (Recomendado)

### Requisitos Previos
- Tener instalado e iniciado **Docker Desktop** en Windows.
- No es necesario tener instalado PHP ni PostgreSQL en tu máquina local.

### 1. Iniciar los contenedores
Desde la raíz del repositorio, ejecuta:
```bash
docker compose up -d --build
```
> Esto descargará la imagen de **PostgreSQL 18** y construirá el contenedor de la aplicación (**PHP 8.3 CLI** con extensiones de PostgreSQL, Composer y Node.js/npm).

### 2. Instalar dependencias de PHP (si no existen)
```bash
docker compose exec app composer install
```

### 3. Ejecutar migraciones y datos iniciales (Seeders)
```bash
docker compose exec app php artisan migrate --seed
```

### 4. Acceso al sistema
- **Aplicación Web:** [http://localhost:8000](http://localhost:8000)
- **Base de Datos PostgreSQL:** Puerto `5432` en `localhost`
  - Host: `127.0.0.1` (desde Windows) o `db` (desde el contenedor)
  - Puerto: `5432`
  - Base de datos: `celix_db`
  - Usuario: `postgres`
  - Contraseña: `admin`

---

## 🛠 Comandos Útiles de Docker

- **Ver estado de los contenedores:**
  ```bash
  docker compose ps
  ```
- **Ver logs en tiempo real:**
  ```bash
  docker compose logs -f app
  docker compose logs -f db
  ```
- **Ejecutar comandos Artisan:**
  ```bash
  docker compose exec app php artisan <comando>
  ```
- **Ejecutar pruebas unitarias / de integración:**
  ```bash
  docker compose exec app php artisan test
  ```
- **Compilar assets con Vite (Frontend):**
  ```bash
  docker compose exec app npm install
  docker compose exec app npm run build
  ```
- **Detener los contenedores:**
  ```bash
  docker compose down
  ```
- **Detener y reiniciar limpiando volúmenes (¡Precaución: borra la base de datos!):**
  ```bash
  docker compose down -v
  ```

---

## Desarrollo Local sin Docker (Alternativa)

Si prefieres ejecutar el proyecto de forma nativa en Windows:
1. Asegúrate de tener PHP 8.2+ con la extensión `pdo_pgsql` habilitada y PostgreSQL 18 corriendo localmente en el puerto 5432.
2. Ingresa a la carpeta `AppWeb`:
   ```bash
   cd AppWeb
   ```
3. Copia el archivo de entorno y genera la clave:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
4. Configura las credenciales de PostgreSQL en `.env` (`DB_HOST=127.0.0.1`).
5. Instala dependencias y corre las migraciones:
   ```bash
   composer install
   npm install
   npm run build
   php artisan migrate --seed
   ```
6. Inicia el servidor:
   ```bash
   php artisan serve
   ```
