<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ClientesController extends Controller
{
    public function index()
    {
        try {
            $clientes = Cliente::all();
            return response()->json($clientes);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los clientes', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ruc' => 'required|string|max:20|unique:clientes,ruc',
            'correo' => 'required|email|unique:clientes,correo',
            'telefono' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cliente = Cliente::create($validator->validated());
            return response()->json($cliente, 201);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error al crear el cliente', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'direccion' => 'sometimes|required|string|max:255',
            'ruc' => 'sometimes|required|string|max:20|unique:clientes,ruc,' . $id,
            'correo' => 'sometimes|required|email|unique:clientes,correo,' . $id,
            'telefono' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cliente->update($validator->validated());
            return response()->json($cliente);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error al actualizar el cliente', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        try {
            $cliente->delete();
            return response()->json(['message' => 'Cliente eliminado correctamente']);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error al eliminar el cliente', 'error' => $e->getMessage()], 500);
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

            $clientes = Cliente::where('nombre', 'LIKE', "%{$busqueda}%")
                ->orWhere('correo', 'LIKE', "%{$busqueda}%")
                ->get();

            return response()->json($clientes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al realizar la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportarAExcel()
    {

        $clientes = Cliente::with('cotizacionesGenerales')->get();
        $fileName = 'clientes_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/public/' . $fileName);
        $headers = [
            'ID',
            'Nombre',
            'Direccion',
            'RUC',
            'Correo',
            'Telefono',
            'Cantidad Cotizaciones Generales',
            'Creado en',
            'Actualizado en',
        ];

        $dataToExport = $clientes->map(function ($cliente) {
            return [
                'ID' => $cliente->id,
                'Nombre' => $cliente->nombre,
                'Direccion' => $cliente->direccion,
                'RUC' => $cliente->ruc,
                'Correo' => $cliente->correo,
                'Telefono' => $cliente->telefono,
                'Cantidad Cotizaciones Generales' => $cliente->cotizacionesGenerales->count(), // Aquí obtenemos el length
                'Creado en' => $cliente->created_at, // Ya están formateados por los accessors del modelo
                'Actualizado en' => $cliente->updated_at, // Ya están formateados por los accessors del modelo
            ];
        });

        SimpleExcelWriter::create($filePath)
            ->addHeader($headers)
            ->addRows($dataToExport->toArray())
            ->close();


        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);

    }
}
