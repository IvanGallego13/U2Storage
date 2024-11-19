<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class JsonController extends Controller
{
    // Función que valida si un string es un JSON válido
    private function isValidJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Lista todos los ficheros JSON válidos en el almacenamiento.
     * Los archivos deben tener la extensión .json y su contenido debe ser un JSON válido.
     *
     * @return JsonResponse
     */
    public function index()
    {   
    // Obtener todos los archivos en storage/app
    $files = Storage::files('app');

    // Filtrar solo archivos con extensión .json y cuyo contenido sea un JSON válido
    $jsonFiles = array_filter($files, function ($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'json' && $this->isValidJson(Storage::get($file));
    });

    // Si no se encuentran archivos JSON válidos, devolver 404
    if (empty($jsonFiles)) {
        return response()->json([
            'mensaje' => 'No se encontraron archivos JSON válidos.'
        ], 404);
    }

    // Devolver el JSON con los nombres de los archivos JSON válidos
    // Se asegura de devolver un array indexado en lugar de un array asociativo
    $jsonFilesList = array_values(array_map(fn($file) => basename($file), $jsonFiles));

    return response()->json([
        'mensaje' => 'Operación exitosa',
        'contenido' => $jsonFilesList, // Esto asegurará que sea un array indexado
    ]);
    }   

    /**
     * Crea un nuevo archivo JSON.
     * Si el archivo ya existe, devuelve un 409.
     * Si el contenido no es un JSON válido, devuelve un 415.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'filename' => 'required|string',
            'content' => 'required|string',
        ]);

        // Verificar si el contenido es un JSON válido
        if (!$this->isValidJson($request->content)) {
            return response()->json(['mensaje' => 'Contenido no es un JSON válido'], 415);
        }

        // Verificar si el archivo ya existe
        if (Storage::exists('app/' . $request->filename)) {
            return response()->json(['mensaje' => 'El fichero ya existe'], 409);
        }

        // Guardar el archivo
        Storage::put('app/' . $request->filename, $request->content);

        return response()->json(['mensaje' => 'Fichero guardado exitosamente']);
    }

    /**
     * Muestra el contenido de un archivo JSON.
     * Si el archivo no existe, devuelve un 404.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        // Verificar si el archivo existe
        if (!Storage::exists('app/' . $id)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        // Leer el contenido del archivo
        $content = Storage::get('app/' . $id);

        // Devolver el contenido del archivo como JSON
        return response()->json([
            'mensaje' => 'Operación exitosa',
            'contenido' => json_decode($content, true),
        ]);
    }

    /**
     * Actualiza el contenido de un archivo JSON.
     * Si el archivo no existe, devuelve un 404.
     * Si el contenido no es válido, devuelve un 415.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id)
    {
        // Validar los datos recibidos
        $request->validate([
            'content' => 'required|string',
        ]);

        // Verificar si el contenido es un JSON válido
        if (!$this->isValidJson($request->content)) {
            return response()->json(['mensaje' => 'Contenido no es un JSON válido'], 415);
        }

        // Verificar si el archivo existe
        if (!Storage::exists('app/' . $id)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        // Actualizar el archivo con el nuevo contenido
        Storage::put('app/' . $id, $request->content);

        return response()->json(['mensaje' => 'Fichero actualizado exitosamente']);
    }

    /**
     * Elimina un archivo JSON.
     * Si el archivo no existe, devuelve un 404.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        // Verificar si el archivo existe
        if (!Storage::exists('app/' . $id)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        // Eliminar el archivo
        Storage::delete('app/' . $id);

        return response()->json(['mensaje' => 'Fichero eliminado exitosamente']);
    }
}
