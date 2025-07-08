<?php

namespace App\Jobs;

use App\Mail\CotizacionEnviada;
use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use Storage;

class EnviarCotizacionJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $cotGen;
    protected $cotizaciones;
    protected $idCliente;

    /**
     * Create a new job instance.
     */
    public function __construct($cotGen, $cotizaciones, $idCliente)
    {
        $this->cotGen = $cotGen;
        $this->cotizaciones = $cotizaciones;
        $this->idCliente = $idCliente;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cliente = Cliente::find($this->idCliente);

        $pdf = Pdf::loadView('pdf.cotizacion', [
            'cliente' => $cliente,
            'cotizacion' => $this->cotGen,
            'detalles' => $this->cotizaciones,
        ]);

        $pdfPath = 'public/cotizacion_' . $this->cotGen['id'] . '.pdf';
        Storage::put($pdfPath, $pdf->output());

        Mail::to($cliente->correo)->send(new CotizacionEnviada(
            $this->cotGen,
            $cliente,
            storage_path('app/' . $pdfPath)
        ));
    }
}
