<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class CsvController extends Controller
{
    /**
     * Lista todos los ficheros CSV de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     */
    public function index()
    {
        $files = Storage::files('app');
    
        // Filtramos solo los archivos CSV
        $csvFiles = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'csv';
        });
    
        // Extraemos solo el nombre de los archivos, sin la ruta completa
        $csvFiles = array_map(function ($file) {
            return basename($file); // Obtiene solo el nombre del archivo
        }, $csvFiles);
    
        return response()->json([
            'mensaje' => 'Operación exitosa',
            'contenido' => array_values($csvFiles) // Asegura que el array esté numerado correctamente
        ]);
    }
    

    /**
     * Recibe por parámetro el nombre de fichero y el contenido CSV y crea un nuevo fichero con ese nombre y contenido en storage/app. 
     * Si el fichero ya existe, devuelve un 409.
     *
     * @param Request $request Contiene los parámetros 'filename' y 'content'.
     * @return JsonResponse La respuesta en formato JSON.
     */
    public function store(Request $request)
    {
    // Validar que el archivo y el contenido estén presentes
    $request->validate([
        'filename' => 'required|string',
        'content' => 'required|string',
    ]);

    $filename = $request->input('filename');
    $content = $request->input('content');

    // Verificar si el archivo ya existe
    if (Storage::exists('app/' . $filename)) {
        return response()->json(['mensaje' => 'El fichero ya existe'], 409);
    }

    // Intentar analizar el contenido CSV. Si el contenido no es válido, devolver un error 415.
    $csvData = str_getcsv($content);

    // Validar que el contenido sea un CSV válido, es decir, que haya al menos una fila
    if (empty($csvData) || !is_array($csvData) || count($csvData) <= 1) {
        return response()->json(['mensaje' => 'Contenido no es un CSV válido'], 415);
    }

    // Guardar el archivo CSV
    Storage::put('app/' . $filename, $content);

    return response()->json(['mensaje' => 'Fichero guardado exitosamente'], 200);
    }


    /**
     * Recibe por parámetro el nombre de un fichero CSV y devuelve su contenido en formato JSON.
     * Si el fichero no existe devuelve un 404.
     *
     * @param string $id Nombre del fichero CSV.
     * @return JsonResponse La respuesta en formato JSON.
     */
    public function show(string $id)
    {
        // Verificar si el archivo existe
        if (!Storage::exists('app/' . $id)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        // Obtener el contenido del archivo CSV
        $content = Storage::get('app/' . $id);

        // Convertir el contenido CSV a un array
        $lines = explode("\n", $content);
        $data = array_map(function ($line) {
            return str_getcsv($line);
        }, $lines);

        // Eliminar la primera fila de encabezados y asignar nombres a las columnas
        $headers = array_shift($data);
        $formattedData = array_map(function ($row) use ($headers) {
            return array_combine($headers, $row);
        }, $data);

        return response()->json([
            'mensaje' => 'Fichero leído con éxito',
            'contenido' => $formattedData
        ]);
    }

    /**
     * Recibe por parámetro el nombre de fichero, el contenido CSV y actualiza el fichero CSV. 
     * Si el fichero no existe devuelve un 404.
     * Si el contenido no es un CSV válido, devuelve un 415.
     *
     * @param Request $request Contiene los parámetros 'filename' y 'content'.
     * @param string $id Nombre del fichero CSV.
     * @return JsonResponse La respuesta en formato JSON.
     */
    public function update(Request $request, string $id)
    {
        // Validar los parámetros 'filename' y 'content'
        $request->validate([
            'filename' => 'required|string',
            'content' => 'required|string',
        ]);

        // Verificar si el archivo existe
        if (!Storage::exists('app/' . $id)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        // Validar si el contenido es un CSV válido
        $lines = explode("\n", $request->content);
        if (count($lines) < 2) {
            return response()->json(['mensaje' => 'Contenido no es un CSV válido'], 415);
        }

        // Intentar actualizar el archivo CSV
        try {
            Storage::put('app/' . $id, $request->content);
            return response()->json(['mensaje' => 'Fichero actualizado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error al actualizar el fichero'], 500);
        }
    }

    /**
     * Recibe por parámetro el nombre de fichero y lo elimina.
     * Si el fichero no existe devuelve un 404.
     *
     * @param string $id Nombre del fichero CSV.
     * @return JsonResponse La respuesta en formato JSON.
     */
    public function destroy(string $id)
    {
        // Verificar si el archivo existe
        if (!Storage::exists('app/' . $id)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        // Intentar eliminar el archivo CSV
        try {
            Storage::delete('app/' . $id);
            return response()->json(['mensaje' => 'Fichero eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error al eliminar el fichero'], 500);
        }
    }
}
