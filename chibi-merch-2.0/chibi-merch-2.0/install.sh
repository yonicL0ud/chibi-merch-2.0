#!/usr/bin/env bash
# ============================================================
#  Chibi Merch 2.0 - instalador automatico
#  Crea Laravel, restaura los archivos de Chibi Merch que
#  Composer sobrescribe, configura MySQL y siembra datos.
# ============================================================
set -euo pipefail

echo ""
echo "============================================"
echo " Chibi Merch 2.0 - instalador"
echo "============================================"
echo ""

# ---------- 1. Crear Laravel ----------
if [ -d "vendor" ] && [ -f "artisan" ]; then
  echo "[1/7] Laravel ya existe. Se omite la creacion."
else
  echo "[1/7] Creando Laravel (tarda 1-3 minutos)..."
  cp README.md /tmp/chibi-readme.bak 2>/dev/null || true
  rm -rf tmp
  composer create-project laravel/laravel tmp --no-interaction --quiet
  cp -a tmp/. .
  rm -rf tmp
  [ -f /tmp/chibi-readme.bak ] && cp /tmp/chibi-readme.bak README.md || true
  echo "      Laravel creado."
fi

# ---------- 2. Restaurar archivos que Composer pisa ----------
echo "[2/7] Restaurando archivos de Chibi Merch..."

mkdir -p routes database/seeders bootstrap app/Providers

cat > bootstrap/app.php <<'BOOTSTRAP_EOF'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Necesario porque la app corre detras del proxy de Codespaces.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
BOOTSTRAP_EOF

cat > routes/web.php <<'ROUTES_EOF'
<?php

use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogoController::class, 'index'])->name('inicio');

Route::get('/producto/{id}', [CatalogoController::class, 'mostrar'])->name('producto.mostrar');

Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::patch('/carrito/actualizar', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
Route::delete('/carrito/quitar/{id}', [CarritoController::class, 'quitar'])->name('carrito.quitar');
Route::delete('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');

Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

Route::resource('productos', ProductoController::class)->except(['show']);
ROUTES_EOF

cat > database/seeders/DatabaseSeeder.php <<'SEEDER_EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ProductoSeeder::class);
    }
}
SEEDER_EOF

cat > app/Providers/AppServiceProvider.php <<'PROVIDER_EOF'
<?php

namespace App\Providers;

use App\Services\CarritoService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Corregir las URLs cuando la app corre detras del proxy de Codespaces.
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // 2. Compartir el contador del carrito con todas las vistas.
        View::composer('layouts.app', function ($view) {
            $view->with(
                'unidadesCarrito',
                app(CarritoService::class)->totalUnidades(
                    (string) session()->getId()
                ),
            );
        });
    }
}
PROVIDER_EOF

echo "      bootstrap, rutas, provider y seeder restaurados."

# ---------- 3. Configurar MySQL ----------
echo "[3/7] Configurando la base de datos..."
for FILE in .env .env.example; do
  [ -f "$FILE" ] || continue
  sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' "$FILE"
  sed -i 's/^# DB_HOST=127.0.0.1/DB_HOST=db/' "$FILE"
  sed -i 's/^DB_HOST=127.0.0.1/DB_HOST=db/' "$FILE"
  sed -i 's/^# DB_USERNAME=root/DB_USERNAME=laravel/' "$FILE"
  sed -i 's/^DB_USERNAME=root/DB_USERNAME=laravel/' "$FILE"
  sed -i 's/^# DB_PASSWORD=/DB_PASSWORD=secret/' "$FILE"
done
echo "      .env y .env.example configurados."

# ---------- 4. Clave de aplicacion ----------
echo "[4/7] Generando clave de aplicacion..."
php artisan key:generate --force --quiet || true
php artisan config:clear --quiet || true

# ---------- 5. Esperar a MySQL ----------
echo "[5/7] Esperando a MySQL..."
DB_LISTO=0
for i in $(seq 1 30); do
  if mysqladmin ping -h db -u laravel -psecret --silent >/dev/null 2>&1; then
    DB_LISTO=1
    break
  fi
  echo "      MySQL aun no responde (intento $i de 30)..."
  sleep 3
done

if [ "$DB_LISTO" -eq 0 ]; then
  echo "      AVISO: MySQL no respondio. Revisa el contenedor db."
  exit 1
fi
echo "      MySQL responde."

# ---------- 6. Migrar y sembrar ----------
echo "[6/7] Creando tablas y datos..."
php artisan migrate --force --quiet
php artisan db:seed --force --quiet
php artisan storage:link --quiet || true
php artisan optimize:clear --quiet || true
echo "      Tablas creadas y 6 productos cargados."

# ---------- 7. Verificacion ----------
echo "[7/7] Verificando..."
php artisan route:list 2>/dev/null | grep -E "inicio|producto|carrito|contacto" | head -20 || true

echo ""
echo "============================================"
echo " LISTO - Chibi Merch 2.0"
echo "============================================"
echo ""
echo " Ver la tienda:"
echo "     php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo " Luego abre la pestana PORTS y haz clic en el icono del globo."
echo ""