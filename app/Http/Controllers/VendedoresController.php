<?php

namespace App\Http\Controllers;

use App\Models\Vendedores;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class VendedoresController extends Controller
{
    public function index()
    {
        try {
            $vendedores = Vendedores::all();
            return response()->json($vendedores, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener los vendedores.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'correo' => 'required|email|unique:vendedores',
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:20',
            ]);
            if ($validated->fails()) {
                return response()->json(['error' => $validated->errors()], 400);
            }

            $vendedor = Vendedores::create($request->all());

            return response()->json($vendedor, 201);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al registrar el vendedor.' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $vendedor = Vendedores::findOrFail($id);
            return response()->json($vendedor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vendedor no encontrado.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener el vendedor.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $vendedor = Vendedores::findOrFail($id);

            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'correo' => 'required|email|unique:vendedores,correo,' . $id,
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:20',
            ]);

            $vendedor->update($request->all());

            return response()->json($vendedor, 200);
        } catch (ValidationValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vendedor no encontrado.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar el vendedor.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $vendedor = Vendedores::findOrFail($id);
            $vendedor->delete();

            return response()->json(['message' => 'Vendedor eliminado correctamente.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vendedor no encontrado.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al eliminar el vendedor.'], 500);
        }
    }

    public function buscar(Request $request)
    {
        try {
            $busqueda = $request->query('search');

            if (!$busqueda) {
                return response()->json([
                    'message' => 'Debe proporcionar una palabra para buscar.'
                ], 400);
            }

            $clientes = Vendedores::where('nombre', 'LIKE', "%{$busqueda}%")
                ->orWhere('correo', 'LIKE', "%{$busqueda}%")
                ->get();

            return response()->json($clientes);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al realizar la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
