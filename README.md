# Trabajo Software 2

Aplicación para administrar y consultar una liga de baloncesto. El administrador gestiona equipos, jugadores, calendario y resultados; los demás usuarios pueden registrarse para consultar los datos y la tabla de posiciones.

## Requisitos

- PHP 8.2 o superior con las extensiones PDO MySQL y SQLite
- Composer
- Node.js y npm
- MySQL o MariaDB

## Configuración local

1. Instala las dependencias:

   ```sh
   composer install
   npm install
   ```

2. Copia `.env.example` como `.env` y configura la conexión a MySQL:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=okey
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña
   ```

   Crea la base de datos `okey` en MySQL/MariaDB antes de ejecutar las migraciones. No compartas ni subas el archivo `.env`.

   En XAMPP, inicia MySQL desde el Panel de Control. Apache es opcional si ejecutas el paso 4, porque Laravel inicia su propio servidor local. Si otro servidor MySQL ya utiliza el puerto `3306`, configura MySQL de XAMPP para usar `3307` en las opciones `[client]` y `[mysqld]` de `C:\xampp\mysql\bin\my.ini`, e indica `DB_PORT=3307` en `.env`. En este equipo se utiliza ese puerto alternativo para no detener el servicio MySQL 8 existente. Puedes crear la base desde phpMyAdmin o con el cliente de XAMPP:

   ```sh
   C:\xampp\mysql\bin\mysql.exe --host=127.0.0.1 --port=3307 --user=root -e "CREATE DATABASE IF NOT EXISTS okey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   ```

   La configuración de este equipo apunta a `okey` en el MariaDB de XAMPP. Su base SQLite local se conserva por separado; MySQL y SQLite no comparten automáticamente los cambios.

3. Genera la clave de la aplicación y crea las tablas:

   ```sh
   php artisan key:generate
   php artisan migrate --seed
   ```

   El seeder agrega equipos, jugadores y partidos de ejemplo. Para una base SQLite local, cambia `DB_CONNECTION` a `sqlite`; Laravel usa `database/database.sqlite`.

   Para servir temporalmente una copia pública de consulta, define `LEAGUE_PUBLIC_READ_ONLY=true` en `.env` y reinicia Laravel. Este modo quita las rutas de inicio de sesión, registro y perfil, y bloquea la administración; al terminar, cambia la opción a `false` y reinicia el servidor para recuperar la gestión normal.

4. Inicia el entorno de desarrollo:

   ```sh
   composer run dev
   ```

   La aplicación se sirve normalmente en `http://127.0.0.1:8000`. Con XAMPP, los datos quedan en el MariaDB de esta computadora; el enlace local no es público y solo funciona mientras los servicios necesarios estén encendidos. GitHub aloja el código fuente, no ejecuta Laravel ni aloja esta base de datos.

## Funcionalidades

- Registro e inicio de sesión.
- Alta, edición y eliminación de equipos, jugadores y partidos para el administrador.
- Consulta de plantillas filtradas por equipo.
- Consulta de partidos, marcadores y tabla de posiciones calculada con los partidos jugados.
- Cuentas registradas con acceso de solo lectura.

El correo administrador se establece en `config/league.php`. La contraseña de administrador no se guarda en el repositorio.

## Pruebas

```sh
php artisan test
```

La suite usa una base SQLite en memoria para no modificar los datos de desarrollo.

## Control de versiones

Repositorio público: [Erik-are21/tarea-software-2](https://github.com/Erik-are21/tarea-software-2). La rama de trabajo `feature/players-by-team` conserva los cambios en commits descriptivos; `main` contiene el commit base. Los cambios se integran mediante pull request cuando GitHub/GitKraken está autenticado.
