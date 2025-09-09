<?php

namespace App\Http\Controllers;

use App\Mail\ProformaEmail;
use App\Models\Cliente;
use App\Models\Proforma;
use App\Models\ProformaDetail;
use App\Models\ProformaIncluye;
use App\Models\Vendedores;
use App\Services\ProformaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProformaController extends Controller
{
    protected $proformaService;

    public function __construct(ProformaService $proformaService)
    {
        $this->proformaService = $proformaService;
    }

    public function index()
    {
        try {
            $proformas = Proforma::with(['cliente', 'vendedor', 'detalles.incluye'])->get();
            return response()->json($proformas);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener proformas', 'details' => $e->getMessage()], 500);
        }
    }

    // Mostrar una proforma específica
    public function show($id)
    {
        try {
            $proforma = Proforma::with(['cliente', 'vendedor', 'detalles.incluye'])->findOrFail($id);
            return response()->json($proforma);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Proforma no encontrada', 'details' => $e->getMessage()], 404);
        }
    }

    public function descargarPdf($id)
    {
        $proforma = Proforma::find($id);

        if (!$proforma) {
            return response()->json(['error' => 'Proforma no encontrada'], 404);
        }

        if (!$proforma->pdf || !Storage::disk('public')->exists($proforma->pdf)) {
            return response()->json(['error' => 'PDF no encontrado'], 404);
        }

        // Obtener ruta absoluta
        $filePath = storage_path('app/public/' . $proforma->pdf);

        return response()->download($filePath, basename($filePath), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // Crear una nueva proforma
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asunto' => 'required|string',
            'lugar_entrega' => 'required|string',
            'forma_pago' => 'required|string',
            'moneda' => 'required|string',
            'fecha_inicial' => 'required|date',
            'fecha_entrega' => 'required|date|after_or_equal:fecha_inicial',
            'subtotal' => 'required|numeric',
            'descuento' => 'nullable|numeric',
            'valor_venta' => 'required|numeric',
            'igv' => 'required|numeric',
            'importe_total' => 'required|numeric',
            'id_cliente' => 'required|exists:clientes,id',
            'id_vendedor' => 'required|exists:vendedores,id',
            'detalles' => 'required|array',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.UM' => 'required|string',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unit' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
            'detalles.*.total' => 'required|numeric|min:0',
            'detalles.*.incluye' => 'array',
            'detalles.*.incluye.*.nombre' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // 🔹 Generar código
            $codigo = $this->proformaService->generarCodigo();

            // 🔹 Calcular días entre fechas
            $fechaInicial = Carbon::parse($request->fecha_inicial);
            $fechaEntrega = Carbon::parse($request->fecha_entrega);
            $dias = $fechaInicial->diffInDays($fechaEntrega);
            Log::info('Fecha'. $fechaEntrega);
            // 🔹 Crear Proforma
            $proforma = Proforma::create([
                'codigo'        => $codigo,
                'asunto'        => $request->asunto,
                'lugar_entrega' => $request->lugar_entrega,
                'forma_pago'    => $request->forma_pago,
                'moneda'        => $request->moneda,
                'fecha_inicial' => $fechaInicial->toDateString(),
                'fecha_entrega' => $fechaEntrega->toDateString(),
                'dias'          => $dias,
                'subtotal'      => $request->subtotal,
                'descuento'     => $request->descuento,
                'valor_venta'   => $request->valor_venta,
                'igv'           => $request->igv,
                'importe_total' => $request->importe_total,
                'id_cliente'    => $request->id_cliente,
                'id_vendedor'   => $request->id_vendedor,
            ]);

            // 🔹 Guardar Detalles
            foreach ($request->detalles as $detalleData) {
                $incluyeItems = $detalleData['incluye'] ?? [];
                unset($detalleData['incluye']);

                $detalleData['id_proforma'] = $proforma->id;
                $detalle = ProformaDetail::create($detalleData);

                foreach ($incluyeItems as $incluye) {
                    ProformaIncluye::create([
                        'nombre'              => $incluye['nombre'],
                        'id_proforma_detail'  => $detalle->id,
                    ]);
                }
            }

            $cliente = Cliente::findOrFail($request->id_cliente);
            $vendedor = Vendedores::findOrFail($request->id_vendedor);

            // 🔹 Generar PDF
            $pdf = Pdf::loadView('pdf.proforma', [
                'cliente'  => $cliente,
                'vendedor' => $vendedor,
                'proforma' => $proforma,
            ]);

            $fileName = 'proforma_' . $proforma->id . '.pdf';
            $pdfPath = 'proformas/' . $fileName; // se guarda en storage/app/public/proformas/
            Storage::disk('public')->put($pdfPath, $pdf->output());

            // 🔹 Actualizar campo pdf en la BD
            $proforma->update([
                'pdf' => $pdfPath,
            ]);

            // 🔹 Enviar correo con PDF adjunto
            Mail::to($cliente->correo)->send(
                new ProformaEmail($proforma, $cliente, $vendedor, storage_path('app/public/' . $pdfPath))
            );

            DB::commit();

            return response()->json([
                'message'      => 'Proforma creada exitosamente',
                'proforma_id'  => $proforma->id,
                'pdf'          => $pdfPath,
                'dias'         => $dias,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Error al crear la proforma',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // Actualizar una proforma
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'asunto' => 'required|string',
            'lugar_entrega' => 'required|string',
            'forma_pago' => 'required|string',
            'moneda' => 'required|string',
            'fecha_inicial' => 'required|date',
            'fecha_entrega' => 'required|date|after_or_equal:fecha_inicial',
            'subtotal' => 'required|numeric',
            'descuento' => 'nullable|numeric',
            'valor_venta' => 'required|numeric',
            'igv' => 'required|numeric',
            'importe_total' => 'required|numeric',
            'id_cliente' => 'required|exists:clientes,id',
            'id_vendedor' => 'required|exists:vendedores,id',
            'detalles' => 'required|array',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.UM' => 'required|string',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unit' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
            'detalles.*.total' => 'required|numeric|min:0',
            'detalles.*.incluye' => 'array',
            'detalles.*.incluye.*.nombre' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $proforma = Proforma::findOrFail($id);

            // 🔹 Calcular días
            $fechaInicial = Carbon::parse($request->fecha_inicial);
            $fechaEntrega = Carbon::parse($request->fecha_entrega);
            $dias = $fechaInicial->diffInDays($fechaEntrega);

            // 🔹 Actualizar proforma
            $proforma->update([
                'asunto'        => $request->asunto,
                'lugar_entrega' => $request->lugar_entrega,
                'forma_pago'    => $request->forma_pago,
                'moneda'        => $request->moneda,
                'fecha_inicial' => $fechaInicial->toDateString(),
                'fecha_entrega' => $fechaEntrega->toDateString(),
                'dias'          => $dias,
                'subtotal'      => $request->subtotal,
                'descuento'     => $request->descuento,
                'valor_venta'   => $request->valor_venta,
                'igv'           => $request->igv,
                'importe_total' => $request->importe_total,
                'id_cliente'    => $request->id_cliente,
                'id_vendedor'   => $request->id_vendedor,
            ]);

            // 🔹 Eliminar detalles previos
            ProformaDetail::where('id_proforma', $proforma->id)->delete();

            // 🔹 Crear nuevos detalles
            foreach ($request->detalles as $detalleData) {
                $incluyeItems = $detalleData['incluye'] ?? [];
                unset($detalleData['incluye']);

                $detalleData['id_proforma'] = $proforma->id;
                $detalle = ProformaDetail::create($detalleData);

                foreach ($incluyeItems as $incluye) {
                    ProformaIncluye::create([
                        'nombre'             => $incluye['nombre'],
                        'id_proforma_detail' => $detalle->id,
                    ]);
                }
            }

            $cliente = Cliente::findOrFail($request->id_cliente);
            $vendedor = Vendedores::findOrFail($request->id_vendedor);

            // 🔹 Regenerar PDF
            $pdf = Pdf::loadView('pdf.proforma', [
                'cliente'  => $cliente,
                'vendedor' => $vendedor,
                'proforma' => $proforma,
            ]);

            $fileName = 'proforma_' . $proforma->id . '.pdf';
            $pdfPath = 'proformas/' . $fileName;

            Storage::disk('public')->put($pdfPath, $pdf->output());

            // 🔹 Actualizar PDF en BD
            $proforma->update([
                'pdf' => $pdfPath,
            ]);

            DB::commit();

            return response()->json([
                'message'      => 'Proforma actualizada exitosamente',
                'proforma_id'  => $proforma->id,
                'pdf'          => $pdfPath,
                'dias'         => $dias,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Error al actualizar la proforma',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // Eliminar una proforma
    public function destroy($id)
    {
        try {
            $proforma = Proforma::findOrFail($id);
            $proforma->delete();
            return response()->json(['message' => 'Proforma eliminada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar la proforma', 'details' => $e->getMessage()], 500);
        }
    }
}
