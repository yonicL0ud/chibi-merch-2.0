<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Libreta Momonga',
                'descripcion' => 'Libreta de espiral con tapa dura y diseño de Momonga. Ideal para tus apuntes y bocetos.',
                'precio' => 45.00,  
                'stock' => 25,
                'imagen' => 'img/productos/libreta-momonga.jpg',
            ],
            [
                'nombre' => 'Llavero Usagi',
                'descripcion' => 'Llavero de Usagi en forma de conejo. Suave, ligero y perfecto para tu mochila o llaves.',
                'precio' => 18.00,   
                'stock' => 40,
                'imagen' => 'img/productos/llavero-usagi.jpg',
            ],
            [
                'nombre' => 'Pack de Stickers Kawaii',
                'descripcion' => 'Pack de 51 stickers waterproof de Chiikawa. Decora tu laptop, cuaderno o estuche.',
                'precio' => 15.00,   
                'stock' => 60,
                'imagen' => 'img/productos/pack-stickers.jpg',
            ],
            [
                'nombre' => 'Peluche Chiikawa',
                'descripcion' => 'Peluche de Chiikawa con material suave y relleno tipo nube. El mejor compañero de estudio.',
                'precio' => 85.00,   
                'stock' => 15,
                'imagen' => 'img/productos/peluche-chiikawa.jpg',
            ],
            [
                'nombre' => 'Peluche Hachiware',
                'descripcion' => 'Peluche de Hachiware en tono calido. Cómodo para abrazar mientras estudias o descansas.',
                'precio' => 95.00,   
                'stock' => 12,
                'imagen' => 'img/productos/peluche-hachiware.avif',
            ],
            [
                'nombre' => 'Taza de Menta Chibi',
                'descripcion' => 'Taza con diseño de menta chibi. Perfecta para tu té o café mientras programas.',
                'precio' => 35.00,   
                'stock' => 30,
                'imagen' => 'img/productos/taza-menta-chibi.jpg',
            ],
        ];

        // Se usa `updateOrCreate` en lugar de `create` para que el seeder
        // pueda ejecutarse varias veces sin duplicar los productos: si el
        // nombre ya existe, actualiza sus datos; si no, lo crea.
        foreach ($productos as $producto) {
            Producto::updateOrCreate(
                ['nombre' => $producto['nombre']],
                [
                    'descripcion' => $producto['descripcion'],
                    'precio' => $producto['precio'],
                    'stock' => $producto['stock'],
                    'imagen' => $producto['imagen'],
                ],
            );
        }
    }
}