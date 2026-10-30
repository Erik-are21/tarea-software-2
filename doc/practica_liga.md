# Documentación técnica: Trabajo Software 2

## 1. Propósito y alcance

Trabajo Software 2 es una aplicación web para administrar y consultar una liga de baloncesto. El administrador mantiene equipos, plantillas, partidos y resultados. Visitantes y cuentas registradas pueden consultar, pero solo el administrador edita. La tabla de posiciones se calcula con partidos «jugados». El calendario de ejemplo ilustra la aplicación; no reemplaza un calendario oficial.

## 2. Arquitectura

El proyecto utiliza Laravel 12, PHP 8.2 o superior, Blade, Eloquent y Vite. El navegador solicita una ruta de `routes/web.php`; el controlador valida la operación, consulta los modelos y entrega datos a una vista. Al guardar, se persiste el cambio y se redirige al listado con un mensaje.

Los modelos son `User`, `Team`, `Player` y `Game`. Un equipo tiene jugadores y partidos locales o visitantes; cada jugador pertenece a un equipo y su número es único en esa plantilla. Un partido relaciona equipos, fecha, estado y marcadores. Las migraciones de `database/migrations` definen tablas y claves foráneas. Los jugadores se eliminan en cascada con su equipo; un equipo con partidos no se elimina, para preservar el calendario.

## 3. Controladores y módulos

`TeamController` gestiona equipos y calcula la tabla: jugados, victorias, empates, derrotas, puntos a favor y en contra, diferencia y porcentaje de victorias. Cuenta solo partidos jugados con ambos marcadores. Ordena por porcentaje, diferencia y puntos anotados.

`PlayerController` lista, crea, actualiza y elimina jugadores. Acepta `team_id` para filtrar una plantilla. Comprueba que el equipo exista, limita el número de camiseta y evita duplicados.

`GameController` mantiene calendario y resultados. Valida equipos distintos y existentes, fecha y marcadores no negativos para un partido jugado. Al programar limpia los marcadores y evita duplicar el mismo encuentro en la misma fecha.

La autenticación usa Laravel Breeze y `RegisteredUserController`. El `Gate` `manage-league` de `AppServiceProvider` compara el correo con `config/league.php`. Las vistas ocultan acciones a otros usuarios y el middleware `auth` más `can:manage-league` protege la escritura en el servidor. Ocultar botones no es la única protección.

## 4. Funcionamiento y ejemplo de código

Las rutas `GET /teams`, `GET /players` y `GET /games` son públicas; crear, editar y eliminar requiere autorización. Cada listado consulta la base de datos al abrirse, por eso muestra cambios del administrador al cargar de nuevo.

La tabla no duplica estadísticas: `TeamController` recorre partidos jugados y suma cada marcador al equipo correspondiente. Una victoria local suma victoria al local y derrota al visitante. Corregir el resultado actualiza la clasificación en la próxima consulta. `LeagueRosterAndScheduleSeeder` carga datos repetibles con `updateOrCreate`.

El permiso se declara en `app/Providers/AppServiceProvider.php`:

```php
Gate::define('manage-league',
    fn (User $user): bool => $user->email === config('league.admin_email'));
```

La comprobación de autorización también se aplica a las rutas de escritura; así los permisos no dependen de la interfaz.

## 5. Persistencia, calidad y decisiones

La aplicación usa SQLite en `database/database.sqlite`; MySQL aún no está configurado, aunque lo recomienda el requisito y lo documenta `.env.example`. Migrar exige configurar acceso local, crear la base y probar la conexión. La contraseña se mantiene en `.env`, excluido de Git.

Inicialmente las pruebas compartían el SQLite de desarrollo y podían borrar sus datos. PHPUnit ahora utiliza SQLite en memoria con `RefreshDatabase`, aislando cada prueba. La suite verifica permisos, formularios, filtros, estadísticas, cambios visibles y seeding; la última ejecución pasó 37 pruebas.

## 6. Ejecución y control de versiones

Con PHP, Composer y Node.js: ejecutar `composer install`, `npm install`, `php artisan key:generate`, `php artisan migrate --seed` y `composer run dev`. `npm run build` compila los recursos y `php artisan test` ejecuta la suite.

Git mantiene el trabajo local en `feature/players-by-team`, en commits descriptivos. Sin remoto configurado, todavía no se publicó en GitHub. Para entregar el enlace, crear el repositorio, añadir `git remote add origin <URL>` y subir con `git push -u origin feature/players-by-team`.
