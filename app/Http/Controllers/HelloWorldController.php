<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelloWorldController extends Controller
{
    public function index()
    {
        try {
            $files = Storage::files();
            return response()->json([
                'mensaje' => 'Listado de ficheros',
                'contenido' => $files,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al listar los archivos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'filename' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'filename.required' => 'El nombre del archivo es obligatorio.',
            'content.required' => 'El contenido del archivo es obligatorio.',
            'filename.max' => 'El nombre del archivo no puede exceder 255 caracteres.',
        ]);

        if (Storage::exists($request->filename)) {
            return response()->json([
                'mensaje' => 'El archivo ya existe',
            ], 409);
        }

        try {
            Storage::put($request->filename, $request->content);
            return response()->json([
                'mensaje' => 'Guardado con éxito',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al guardar el archivo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $filename)
    {
        if (!Storage::exists($filename)) {
            return response()->json([
                'mensaje' => 'Archivo no encontrado',
            ], 404);
        }

        try {
            $content = Storage::get($filename);
            return response()->json([
                'mensaje' => 'Archivo leído con éxito',
                'contenido' => $content,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al leer el archivo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $filename)
    {
        $request->validate([
            'content' => 'required|string',
        ], [
            'content.required' => 'El contenido del archivo es obligatorio.',
        ]);

        if (!Storage::exists($filename)) {
            return response()->json([
                'mensaje' => 'El archivo no existe',
            ], 404);
        }

        try {
            Storage::put($filename, $request->content);
            return response()->json([
                'mensaje' => 'Actualizado con éxito',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al actualizar el archivo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $filename)
    {
        if (!Storage::exists($filename)) {
            return response()->json([
                'mensaje' => 'El archivo no existe',
            ], 404);
        }

        try {
            Storage::delete($filename);
            return response()->json([
                'mensaje' => 'Eliminado con éxito',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al eliminar el archivo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
