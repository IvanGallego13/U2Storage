<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelloWorldController extends Controller
{
    /**
     * Lista todos los ficheros de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: Un array con los nombres de los ficheros.
     */
    public function index()
    {
    {
    // Obtenemos todos los nombres de los ficheros en la carpeta 'storage/app'
    $files = Storage::files();

    // Retornamos la respuesta en formato JSON
    return response()->json([
        'mensaje' => 'Listado de ficheros',
        'contenido' => $files,
    ]);
    }

    }

     /**
     * Recibe por parámetro el nombre de fichero y el contenido. Devuelve un JSON con el resultado de la operación.
     * Si el fichero ya existe, devuelve un 409.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function store(Request $request)
    {
         // Validamos que los parámetros 'filename' y 'content' estén presentes
    $request->validate([
        'filename' => 'required|string',
        'content' => 'required|string',
    ]);

    // Comprobamos si el archivo ya existe
    if (Storage::exists($request->filename)) {
        return response()->json([
            'mensaje' => 'El archivo ya existe',
        ], 409);
    }

    // Almacenamos el contenido en el archivo
    Storage::put($request->filename, $request->content);

    // Respondemos con un mensaje de éxito
    return response()->json([
        'mensaje' => 'Guardado con éxito',
    ]);
    }

     /**
     * Recibe por parámetro el nombre de fichero y devuelve un JSON con su contenido
     *
     * @param name Parámetro con el nombre del fichero.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: El contenido del fichero si se ha leído con éxito.
     */
    public function show(string $filename)
    {
         // Comprobamos si el archivo existe
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'Archivo no encontrado',
        ], 404);
    }

    // Obtenemos el contenido del archivo
    $content = Storage::get($filename);

    // Retornamos el contenido en formato JSON
    return response()->json([
        'mensaje' => 'Archivo leído con éxito',
        'contenido' => $content,
    ]);
    }

    /**
     * Recibe por parámetro el nombre de fichero, el contenido y actualiza el fichero.
     * Devuelve un JSON con el resultado de la operación.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function update(Request $request, string $filename)
    {
        // Validamos que el parámetro 'content' esté presente
    $request->validate([
        'content' => 'required|string',
    ]);

    // Comprobamos si el archivo existe
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo no existe',
        ], 404);
    }

    // Actualizamos el contenido del archivo
    Storage::put($filename, $request->content);

    // Respondemos con un mensaje de éxito
    return response()->json([
        'mensaje' => 'Actualizado con éxito',
    ]);
    }

    /**
     * Recibe por parámetro el nombre de ficher y lo elimina.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function destroy(string $filename)
    {
         // Comprobamos si el archivo existe
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo no existe',
        ], 404);
    }

    // Eliminamos el archivo
    Storage::delete($filename);

    // Respondemos con un mensaje de éxito
    return response()->json([
        'mensaje' => 'Eliminado con éxito',
    ]);
    }
}
