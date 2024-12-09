<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class CsvController extends Controller
{
    public function index(): JsonResponse
    {
        $files = Storage::disk('local')->files();
        $csvFiles = array_filter($files, fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'csv');

        return response()->json([
        'mensaje' => 'Listado de ficheros',
        'contenido' => array_values($csvFiles),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'filename' => 'required|string',
            'content' => 'required|string',
        ]);

        $filename = $request->input('filename');
        $content = $request->input('content');

        // Guardar archivo en 'storage/app'
        $path = "app/$filename";

        if (Storage::disk('local')->exists($path)) {
            return response()->json(['mensaje' => 'El archivo ya existe'], 409);
        }

        Storage::disk('local')->put($path, $content);

        return response()->json(['mensaje' => 'Guardado con éxito'], 200);
    }

    public function show(string $id): JsonResponse
    {
        // Buscar el archivo en 'storage/app'
        $path = "app/$id";

        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        $content = Storage::disk('local')->get($path);
        $lines = array_filter(explode("\n", trim($content)));

        if (count($lines) < 2) {
            return response()->json([
                'mensaje' => 'Fichero leído con éxito',
                'contenido' => [],
            ], 200);
        }

        $headers = str_getcsv(array_shift($lines));
        $data = [];

        foreach ($lines as $line) {
            $row = str_getcsv($line);
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }

        return response()->json([
            'mensaje' => 'Fichero leído con éxito',
            'contenido' => $data,
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        // Buscar el archivo en 'storage/app'
        $path = "app/$id";

        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->input('content');
        Storage::disk('local')->put($path, $content);

        return response()->json(['mensaje' => 'Fichero actualizado exitosamente'], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        // Buscar el archivo en 'storage/app'
        $path = "app/$id";

        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        Storage::disk('local')->delete($path);

        return response()->json(['mensaje' => 'Fichero eliminado exitosamente'], 200);
    }
}
