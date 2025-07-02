<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionCaja;
use App\Models\CotizacionDetail;
use App\Models\CotizacionGeneral;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CotizacionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string',
            'precio_total' => 'required|numeric',
            'fecha_inicial' => 'required|date',
            'fecha_final' => 'required|date',
            'id_cliente' => 'required|exists:clientes,id',
            'dias' => 'required|integer',
            'cotizaciones' => 'required|array',
            'cotizaciones.*.servicios' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 1. Crear cotización general
            $cotGen = CotizacionGeneral::create([
                'descripcion' => $request->descripcion,
                'monto_total' => $request->precio_total,
                'fecha_inicial' => $request->fecha_inicial,
                'fecha_final' => $request->fecha_final,
                'dias_entrega' => $request->dias,
                'id_cliente' => $request->id_cliente,
            ]);

            // 2. Recorrer cada cotización
            foreach ($request->cotizaciones as $cotizacionData) {
                $cot = Cotizacion::create([
                    'id_cotizacion_general' => $cotGen->id,
                    'descripcion' => $cotizacionData['descripcion'],
                    'cantidad' => $cotizacionData['cantidad'],
                    'gg' => $cotizacionData['gg'],
                    'utilidad' => $cotizacionData['utilidad'],
                    'costo_directo' => $cotizacionData['costo_directo'],
                    'total_cotizaciones' => $cotizacionData['precio_total'],
                ]);

                // 3. Recorrer servicios (detalles)
                foreach ($cotizacionData['servicios'] as $servicio) {
                    $detalle = CotizacionDetail::create([
                        'descripcion' => $servicio['descripcion'],
                        'id_cotizacion' => $cot->id,
                        'id_servicio' => $servicio['servicioId'],
                        'precio_total' => $servicio['subtotal'],
                    ]);

                    // 4. Guardar cotizacion_caja según tipo
                    CotizacionCaja::create([
                        'id_cotizacion_detail' => $detalle->id,
                        'total' => $servicio['subtotal'],
                        'cantidad' => $servicio['tipo'] !== 'AREA' ? $servicio['cantidad'] : null,
                        'precio_unitario' => $servicio['tipo'] !== 'AREA' ? $servicio['precio_unit'] : null,
                        'horas_habiles' => $servicio['tipo'] === 'AREA' ? $servicio['horas'] : null,
                        'hora_habil_costo' => $servicio['tipo'] === 'AREA' ? $servicio['costo'] : null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Cotización guardada exitosamente',
                'id' => $cotGen->id,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al guardar la cotización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Carga la cotización general con todas las relaciones necesarias:
            // - cliente: para obtener información del cliente.
            // - cotizaciones: las cotizaciones individuales asociadas a la general.
            // - cotizaciones.detalles.cajas: los detalles de caja dentro de cada cotización individual.
            // - cotizaciones.detalles.servicio: el servicio asociado a cada detalle.
            $cotizacionGeneral = CotizacionGeneral::with([
                'cliente',
                'cotizaciones.detalles.cajas',
                'cotizaciones.detalles.servicio'
            ])->findOrFail($id);

            // Transforma la estructura de la cotización general para que coincida con CotizacionAgregarInterface
            $transformedCotizacion = [
                'id' => $cotizacionGeneral->id,
                'descripcion' => $cotizacionGeneral->descripcion,
                'precio_total' => (float) $cotizacionGeneral->monto_total, // Asegura que sea un flotante
                'fecha_inicial' => $cotizacionGeneral->fecha_inicial,
                'fecha_final' => $cotizacionGeneral->fecha_final,
                'id_cliente' => (int) $cotizacionGeneral->id_cliente, // Asegura que sea un entero
                'dias' => (int) $cotizacionGeneral->dias_entrega, // Asegura que sea un entero

                'cliente' => $cotizacionGeneral->cliente,
                'cotizaciones' => $cotizacionGeneral->cotizaciones->map(function ($cotizacion) {
                    return [
                        'id' => (string) $cotizacion->id, // El ID se convierte a string según la interfaz JS
                        'descripcion' => $cotizacion->descripcion,
                        'cantidad' => (int) $cotizacion->cantidad,
                        'costo_directo' => (float) $cotizacion->costo_directo,
                        'utilidad' => (float) $cotizacion->utilidad,
                        'gg' => (float) $cotizacion->gg,
                        'precio_total' => (float) $cotizacion->total_cotizaciones,
                        // Calcula precio_unit: total_cotizaciones / cantidad. Maneja división por cero.
                        'precio_unit' => (float) ($cotizacion->cantidad > 0 ? $cotizacion->total_cotizaciones / $cotizacion->cantidad : 0),

                        // Mapea la colección de 'detalles' (del modelo CotizacionDetail)
                        // a la estructura de 'CotizacionAgregarServicio[]'
                        'servicios' => $cotizacion->detalles->map(function ($detalle) {
                            // Intenta obtener la primera 'caja' asociada al detalle.
                            // Esto es clave porque 'CotizacionAgregarServicio' parece consolidar
                            // datos que en Laravel pueden estar en 'CotizacionCaja'.
                            $firstCaja = $detalle->cajas->first();

                            return [
                                'id' => (string) $detalle->id, // El ID se convierte a string
                                'servicioId' => (int) $detalle->id_servicio,
                                'descripcion' => $detalle->descripcion,
                                'subtotal' => (float) $detalle->precio_total,
                                // Obtiene el tipo del servicio si existe la relación, de lo contrario null
                                'tipo' => $detalle->servicio ? $detalle->servicio->tipo : null,
                                // Obtiene 'horas' de la primera caja, si existe, de lo contrario 0
                                'horas' => $firstCaja ? (float) $firstCaja->horas_habiles : 0,
                                // Obtiene 'costo' de la primera caja, si existe, de lo contrario 0
                                'costo' => $firstCaja ? (float) $firstCaja->hora_habil_costo : 0,
                                // Obtiene 'cantidad' de la primera caja, si existe, de lo contrario 0
                                'cantidad' => $firstCaja ? (int) $firstCaja->cantidad : 0,
                                // Obtiene 'precio_unit' de la primera caja, si existe, de lo contrario 0
                                'precio_unit' => $firstCaja ? (float) $firstCaja->precio_unitario : 0,
                            ];
                        })->values()->all(), // Convierte la colección mapeada a un array simple
                    ];
                })->values()->all(), // Convierte la colección mapeada a un array simple
            ];

            return response()->json($transformedCotizacion);
        } catch (\Exception $e) {
            // Manejo de errores: si la cotización no se encuentra o hay otro error
            return response()->json([
                'message' => 'Error al obtener la cotización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string',
            'precio_total' => 'required|numeric',
            'fecha_inicial' => 'required|date',
            'fecha_final' => 'required|date',
            'id_cliente' => 'required|exists:clientes,id',
            'dias' => 'required|integer',
            'cotizaciones' => 'required|array',
            'cotizaciones.*.servicios' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $cotGen = CotizacionGeneral::findOrFail($id);

            // Actualizar CotizacionGeneral con todos los campos relevantes
            $cotGen->update([
                'descripcion' => $request->descripcion,
                'monto_total' => $request->precio_total,
                'fecha_inicial' => $request->fecha_inicial, // Campo agregado
                'fecha_final' => $request->fecha_final,     // Campo agregado
                'dias_entrega' => $request->dias,
                'id_cliente' => $request->id_cliente,
            ]);

            // Eliminar anteriores y recrear (alternativamente podrías actualizar en cascada si quieres)
            // Es importante eliminar en el orden correcto para evitar problemas de claves foráneas
            foreach ($cotGen->cotizaciones as $cot) {
                foreach ($cot->detalles as $det) {
                    CotizacionCaja::where('id_cotizacion_detail', $det->id)->delete();
                    $det->delete();
                }
                $cot->delete();
            }

            // Re-crear estructura
            foreach ($request->cotizaciones as $cotizacionData) {
                $cot = Cotizacion::create([
                    'id_cotizacion_general' => $cotGen->id,
                    'descripcion' => $cotizacionData['descripcion'], // Campo agregado
                    'cantidad' => $cotizacionData['cantidad'],       // Campo agregado
                    'gg' => $cotizacionData['gg'],
                    'utilidad' => $cotizacionData['utilidad'],
                    'costo_directo' => $cotizacionData['costo_directo'], // Campo agregado
                    'total_cotizaciones' => $cotizacionData['precio_total'],
                ]);

                foreach ($cotizacionData['servicios'] as $servicio) {
                    $detalle = CotizacionDetail::create([
                        'descripcion' => $servicio['descripcion'],
                        'id_cotizacion' => $cot->id,
                        'id_servicio' => $servicio['servicioId'],
                        'precio_total' => $servicio['subtotal'],
                    ]);

                    CotizacionCaja::create([
                        'id_cotizacion_detail' => $detalle->id,
                        'total' => $servicio['subtotal'],
                        'cantidad' => $servicio['tipo'] !== 'AREA' ? $servicio['cantidad'] : null,
                        'precio_unitario' => $servicio['tipo'] !== 'AREA' ? $servicio['precio_unit'] : null,
                        'horas_habiles' => $servicio['tipo'] === 'AREA' ? $servicio['horas'] : null,
                        'hora_habil_costo' => $servicio['tipo'] === 'AREA' ? $servicio['costo'] : null,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Cotización actualizada correctamente']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la cotización',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function index()
    {
        try {
            $datos = CotizacionGeneral::with([
                'cliente',
                'cotizaciones.detalles.cajas',
                'cotizaciones.detalles.servicio'
            ])->orderBy('id', 'desc')->get();

            // Formatear los datos para la tabla
            

            return response()->json($datos);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al obtener las cotizaciones',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
