<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServicioController extends Controller
{
    public function index()
    {
        try {
            $servicios = Servicio::all();
            return response()->json($servicios);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los servicios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $servicio = Servicio::with('detalles')->find($id);

            if (!$servicio) {
                return response()->json(['message' => 'Servicio no encontrado'], 404);
            }

            return response()->json($servicio);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al buscar el servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:SERVICIO,AREA,OTROS',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $servicio = Servicio::create($validator->validated());


            return response()->json($servicio, 201);
        } catch (QueryException $e) {

            return response()->json([
                'message' => 'Error al crear el servicio',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Ocurrió un error inesperado al crear el servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json(['message' => 'Servicio no encontrado'], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:255',
                'tipo' => 'sometimes|required|in:SERVICIO,AREA,OTROS',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $servicio->update($validator->validated());

            

            return response()->json($servicio);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al actualizar el servicio',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error inesperado al actualizar el servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $servicio = Servicio::find($id);

            if (!$servicio) {
                return response()->json(['message' => 'Servicio no encontrado'], 404);
            }

            $servicio->delete();

            return response()->json(['message' => 'Servicio eliminado correctamente']);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al eliminar el servicio',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error inesperado al eliminar el servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
