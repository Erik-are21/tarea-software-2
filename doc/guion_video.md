# Guion para explicar el proyecto en video

Duración sugerida: 4–6 minutos. Abre la aplicación y `doc/practica_liga.md` antes de grabar. No muestres el archivo `.env`, contraseñas ni datos privados.

## Parte 1 — Presentación (20–30 s)

**En pantalla:** página de equipos.

**Explica:** «Este proyecto se llama Trabajo Software 2. Es una aplicación para administrar una liga de baloncesto: el administrador mantiene equipos, jugadores y partidos; los visitantes pueden consultar la información y la tabla de posiciones».

## Parte 2 — Tecnologías y arquitectura (40–60 s)

**En pantalla:** `routes/web.php`, luego `app/Http/Controllers` y `app/Models`.

**Explica:** «Está construido con Laravel y PHP. Las rutas reciben la solicitud, los controladores procesan la operación, los modelos Eloquent acceden a la base de datos y las vistas Blade presentan el resultado. Los modelos principales son Team, Player, Game y User».

## Parte 3 — Consulta y plantillas (40–60 s)

**En pantalla:** páginas de jugadores y partidos; elige un equipo en el filtro.

**Explica:** «Las listas de equipos, jugadores y partidos son de consulta pública. En jugadores se puede filtrar la plantilla por equipo. La aplicación consulta los registros guardados en cada solicitud, así los cambios del administrador aparecen al volver a cargar la página».

## Parte 4 — Permisos (40–60 s)

**En pantalla:** `config/league.php`, `AppServiceProvider.php` y las rutas protegidas. No abras `.env`.

**Explica:** «Las cuentas nuevas sirven para consultar, no para editar. El permiso de gestión se comprueba en el servidor con el Gate `manage-league`, que compara la cuenta iniciada con el correo administrador. Las rutas de escritura también exigen autenticación y autorización».

## Parte 5 — Marcadores y estadísticas (50–70 s)

**En pantalla:** tabla de posiciones y `TeamController.php`.

**Explica:** «Al registrar un partido se valida que los equipos sean distintos y que los puntos sean válidos. La tabla calcula victorias, derrotas, puntos a favor, puntos en contra y diferencia solo con partidos jugados. Como los cálculos se hacen al consultar, una corrección del marcador se refleja automáticamente».

## Parte 6 — Base de datos y pruebas (40–60 s)

**En pantalla:** `config/database.php`, migraciones y terminal ejecutando `php artisan test`.

**Explica:** «En este entorno la aplicación usa SQLite en `database/database.sqlite`. La actividad pide MySQL o MariaDB, así que esa migración queda pendiente de configurar una conexión válida; no debo afirmar que está en MySQL. PHPUnit usa una SQLite en memoria para que las pruebas no borren los datos de desarrollo. La suite comprobada tiene 37 pruebas aprobadas».

## Parte 7 — Git y cierre (30–45 s)

**En pantalla:** `git log --oneline` y `git status --short --branch`.

**Explica:** «El progreso se separó en commits descriptivos en la rama `feature/players-by-team`. El repositorio existe localmente, pero todavía no tiene remoto; por eso aún no hay enlace de GitHub ni pull request. Para terminar la entrega se debe crear el repositorio remoto y subir esta rama».

**Cierre sugerido:** «Con esto mostré cómo se consulta y administra la liga, cómo se protegen las operaciones y qué pasos quedan para completar la publicación en MySQL y GitHub».
