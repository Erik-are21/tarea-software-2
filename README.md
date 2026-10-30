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

3. Genera la clave de la aplicación y crea las tablas:

   ```sh
   php artisan key:generate
   php artisan migrate --seed
   ```

   El seeder agrega equipos, jugadores y partidos de ejemplo. Para una base SQLite local, cambia `DB_CONNECTION` a `sqlite`; Laravel usa `database/database.sqlite`.

4. Inicia el entorno de desarrollo:

   ```sh
   composer run dev
   ```

   La aplicación se sirve normalmente en `http://127.0.0.1:8000`.

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
