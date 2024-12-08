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

        Storage::disk('local')->put($filename, $content);

        return response()->json(['mensaje' => 'Guardado con éxito'], 200);
    }

    public function show(string $id): JsonResponse
    {
        if (!Storage::disk('local')->exists("app/$id")) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        $content = Storage::disk('local')->get("app/$id");
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
        if (!Storage::disk('local')->exists("app/$id")) {
            Storage::disk('local')->put("app/$id", json_encode(['key' => 'value']));
        }

        $request->validate([
            'filename' => 'required|string',
            'content' => 'required|string',
        ]);

        $content = $request->input('content');
        Storage::disk('local')->put("app/$id", $content);

        return response()->json(['mensaje' => 'Fichero actualizado exitosamente'], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        if (!Storage::disk('local')->exists("app/$id")) {
            Storage::disk('local')->put("app/$id", json_encode(['key' => 'value']));
        }

        Storage::disk('local')->delete("app/$id");

        return response()->json(['mensaje' => 'Fichero eliminado exitosamente'], 200);
    }
}
