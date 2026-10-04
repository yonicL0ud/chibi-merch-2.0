# Chibi Merch 2.0

Tienda kawaii desarrollada con **Laravel + MySQL** para la asignatura de
Programación Web (Universidad Continental).

Este proyecto implementa una **arquitectura por capas**, que es lo que pide la
rúbrica de la Unidad 2: un controlador no habla nunca directamente con la base
de datos.

---

## Arquitectura por capas

```
Navegador
    │  petición HTTP
    ▼
CONTROLADOR  app/Http/Controllers/     Presentación
    │  delega
    ▼
SERVICIO     app/Services/             Lógica de negocio
    │  delega
    ▼
REPOSITORIO  app/Repositories/         Acceso a datos
    │  usa
    ▼
MODELO       app/Models/               Definición de las entidades
    │
    ▼
MySQL 8.0
```

| Capa | Carpeta | Responsabilidad |
|------|---------|-----------------|
| Controlador | `app/Http/Controllers/` | Recibe la petición, pide la acción y devuelve la vista o redirección. Sin lógica de negocio. |
| Servicio | `app/Services/` | Todas las reglas de negocio: stock, totales, validación de productos. Es la API que usan los controladores. |
| Repositorio | `app/Repositories/` | Consulta y persiste en MySQL. No decide nada. |
| Modelo | `app/Models/` | Describe cada tabla y sus relaciones. |

---

## Archivos y qué hace cada uno

### Capa de modelos — `app/Models/`

| Archivo | Qué hace |
|---------|----------|
| `Producto.php` | Entidad de producto. Declara `$fillable` y la relación `items()`. |
| `CarritoItem.php` | Línea de carrito. Declara `$fillable` y la relación `producto()`. |
| `Mensaje.php` | Mensaje del formulario de contacto. |

### Capa de acceso a datos — `app/Repositories/`

| Archivo | Qué hace |
|---------|----------|
| `ProductoRepository.php` | Lee, crea, actualiza y borra productos. Guarda las imágenes subidas. |
| `CarritoRepository.php` | Lee y escribe las líneas de `carrito_items`. Suma unidades. |
| `MensajeRepository.php` | Guarda y lista los mensajes de contacto. |

### Capa de lógica de negocio — `app/Services/`

| Archivo | Qué hace |
|---------|----------|
| `CarritoService.php` | **El más importante.** Verifica stock, acumula cantidades, calcula subtotales y total, vacía el carrito. Usa transacciones. |
| `ProductoService.php` | Reglas de validación del producto y la regla de la imagen por defecto. |
| `MensajeService.php` | Reglas de validación del contacto y registro del mensaje. |

### Excepción de negocio — `app/Exceptions/`

| Archivo | Qué hace |
|---------|----------|
| `StockInsuficienteException.php` | Error previsto: no hay stock. El servicio lo lanza, el controlador lo captura y muestra el mensaje. |

### Capa de presentación — `app/Http/Controllers/`

| Archivo | Qué hace |
|---------|----------|
| `CatalogoController.php` | Muestra el catálogo y la ficha. Usa el repositorio directo: aquí no hay reglas de negocio. |
| `CarritoController.php` | Agregar, cambiar cantidad, quitar y vaciar. No comprueba stock: lo hace `CarritoService`. |
| `ProductoController.php` | CRUD de productos. No define reglas: las pide a `ProductoService`. |
| `ContactoController.php` | Muestra y recibe el formulario de contacto. |

### Configuración

| Archivo | Qué hace |
|---------|----------|
| `bootstrap/app.php` | Declara la ruta web y confía en el proxy de Codespaces. |
| `app/Providers/AppServiceProvider.php` | Corrige las URLs detrás del proxy e inyecta el contador del carrito en todas las vistas. |

### Base de datos — `database/`

| Archivo | Qué hace |
|---------|----------|
| `migrations/..._create_productos_table.php` | Tabla `productos`. |
| `migrations/..._create_mensajes_table.php` | Tabla `mensajes`. |
| `migrations/..._create_carrito_items_table.php` | Tabla `carrito_items`, con llave foránea a productos y borrado en cascada. |
| `seeders/ProductoSeeder.php` | Carga los 6 productos con precios en soles. |

### Vistas — `resources/views/`

| Archivo | Qué muestra |
|---------|-------------|
| `layouts/app.blade.php` | Estructura base, menú y contador del carrito. |
| `parciales/alertas.blade.php` | Mensajes de éxito, error y errores de validación. |
| `catalogo/index.blade.php` | La tienda. |
| `catalogo/mostrar.blade.php` | Ficha de un producto. |
| `productos/index.blade.php` | Listado del CRUD. |
| `productos/form.blade.php` | Alta y edición. |
| `carrito/index.blade.php` | El carrito con su resumen. |
| `contacto/index.blade.php` | Formulario de contacto. |

---

## El carrito en la base de datos

El carrito **no** se guarda en la sesión de PHP. Se guarda en la tabla
`carrito_items`, y cada línea pertenece a una sesión mediante `session_id`.

Ventajas frente a guardarlo en la sesión:

1. Sobrevive al cierre del navegador.
2. Se puede demostrar con una consulta SQL.
3. Se puede migrar a un carrito por usuario solo agregando la llave
   foránea `user_id`.

---

## Rutas

| Ruta | Para qué sirve |
|------|----------------|
| `/` | Catálogo |
| `/producto/{id}` | Ficha del producto |
| `/carrito` | Ver el carrito |
| `/carrito/agregar` | Agregar un producto |
| `/carrito/actualizar` | Cambiar la cantidad |
| `/carrito/quitar/{id}` | Quitar un producto |
| `/carrito/vaciar` | Vaciar el carrito |
| `/contacto` | Formulario de contacto |
| `/productos` | CRUD de productos |

---

## Cómo ejecutarlo en GitHub Codespaces

### 1. Estructura del repositorio

El `.devcontainer` debe estar en la **raíz** del repositorio, porque
Codespaces solo lo busca ahí:

```
chibi-merch/          ← raíz
├── .devcontainer/
└── chibi-merch-2.0/   ← aquí vive Laravel
```

### 2. Instalar

En la terminal del Codespace:

```bash
bash install.sh
```

El script crea Laravel, restaura los archivos que Composer sobrescribe,
configura MySQL en `.env`, genera la clave, espera a la base de datos,
crea las tablas, carga los 6 productos y enlaza el almacenamiento.

### 3. Ver la página

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Luego abre la pestaña **PORTS** y haz clic en el icono del **globo**.

---

## Productos

| Producto | Precio (S/) |
|---------------------------|------------|
| Libreta Momonga           | 45.00 |
| Llavero Usagi             | 18.00 |
| Pack de Stickers Kawaii   | 15.00 |
| Peluche Chiikawa          | 85.00 |
| Peluche Hachiware         | 95.00 |
| Taza de Menta Chibi       | 35.00 |

---

## Qué NO incluye

- No hay usuarios, registro ni inicio de sesión.
- El CRUD de `/productos` es de acceso público: cualquier visitante puede
  editar o borrar productos. Es una falla de seguridad conocida; la
  solución futura es Laravel Breeze.
- No hay pasarela de pago: la compra es simulada.
- No hay AJAX ni llamadas a APIs externas.
- No hay pruebas automatizadas.

---

## Problemas frecuentes

**`could not find driver`** — MySQL no está listo. Espera y repite
`php artisan migrate --force`.

**`SQLSTATE[HY000] [2002] Connection refused`** — El contenedor de MySQL no
ha arrancado.

**`ERROR 2026 (HY000): TLS/SSL error`** — Falta desactivar la verificación
del certificado del contenedor. Crea `~/.my.cnf` con `[client]`,
`skip-ssl = true` y `ssl-verify-server-cert = false`.

**El carrito aparece vacío en otra pestaña** — Es correcto: cada sesión tiene
su propio carrito. Es la comportamiento esperado.

**`419 Page Expired`** — Se venció la sesión. Recarga con F5.

**El navegador no muestra la imagen del peluche Hachiware** — Ese archivo es
formato AVIF. Chrome, Firefox, Edge y Safari 16+ lo muestran.

**502 al abrir la página** — El servidor no está corriendo. Ejecuta otra vez
`php artisan serve --host=0.0.0.0 --port=8000`.