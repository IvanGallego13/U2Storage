<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

// Ruta para servir el archivo `index.html`
Route::get('/', function () {
    $file = realpath(__DIR__ . '/../../../frontend/index.html'); // Ruta al index.html
    if (!File::exists($file)) {
        abort(404, 'Archivo index.html no encontrado.');
    }
    return response(File::get($file), 200)->header('Content-Type', 'text/html');
});

// Ruta para servir archivos estáticos (CSS, JS, imágenes)
Route::get('/static/{file}', function ($file) {
    $path = realpath(__DIR__ . '/../../../frontend/' . $file);
    if ($path && File::exists($path)) {
        $mimeType = mime_content_type($path);

        // Forzar MIME para CSS y JS
        if (str_ends_with($file, '.js')) {
            $mimeType = 'application/javascript';
        } elseif (str_ends_with($file, '.css')) {
            $mimeType = 'text/css';
        }

        return response(File::get($path), 200)->header('Content-Type', $mimeType);
    }
    abort(404, 'Archivo estático no encontrado.');
});