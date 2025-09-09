<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #003366;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
        .company-info {
            background-color: #0066cc;
            color: white;
            padding: 10px 20px;
            font-size: 12px;
        }
        .proforma-title {
            background-color: #f0f0f0;
            text-align: center;
            padding: 15px;
            font-weight: bold;
            font-size: 18px;
            color: #003366;
        }
        .info-section {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px 10px;
            font-size: 12px;
            border: 1px solid #ddd;
        }
        .info-table .label {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 25%;
        }
        .detail-table {
            margin-top: 20px;
        }
        .detail-table th {
            background-color: #003366;
            color: white;
            padding: 10px 5px;
            text-align: center;
            font-size: 12px;
            border: 1px solid #003366;
        }
        .detail-table td {
            padding: 8px 5px;
            text-align: center;
            font-size: 11px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .detail-table .description {
            text-align: left;
            width: 40%;
        }
        .incluye-list {
            text-align: left;
            font-size: 10px;
            margin: 5px 0;
            padding-left: 10px;
        }
        .incluye-list li {
            margin: 2px 0;
        }
        .totals-section {
            margin-top: 20px;
            text-align: right;
        }
        .totals-table {
            margin-left: auto;
            width: 300px;
        }
        .totals-table td {
            padding: 5px 10px;
            font-size: 12px;
            border: 1px solid #ddd;
        }
        .totals-table .label {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: right;
        }
        .total-final {
            background-color: #003366;
            color: white;
            font-weight: bold;
        }
        .terms-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            font-size: 11px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 20px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>P&P INGENIERÍA Y FABRICACIÓN</h1>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div>Jr. TAMBO INGA-POMALITO N° 541</div>
            <div>HUARAZ - ANCASH - PERU</div>
            <div>Telf: 043-8544</div>
            <div>PERU: QRZ INTRANET</div>
        </div>
        
        <!-- Proforma Title -->
        <div class="proforma-title">
            PROFORMA<br>
            Nro 003-00004171
        </div>
        
        <!-- Client and Proforma Info -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label">Señor:</td>
                    <td>{{ $proforma->cliente->nombre }}</td>
                    <td class="label">Fecha:</td>
                    <td>{{ date('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Dirección:</td>
                    <td>{{ $proforma->cliente->direccion }}</td>
                    <td class="label">Moneda:</td>
                    <td>{{ $proforma->moneda }}</td>
                </tr>
                <tr>
                    <td class="label">RUC:</td>
                    <td>{{ $proforma->cliente->ruc }}</td>
                    <td class="label">Vendedor:</td>
                    <td>{{ $proforma->vendedor->nombre }} {{ $proforma->vendedor->apellido }}</td>
                </tr>
                <tr>
                    <td class="label">Teléfono:</td>
                    <td>{{ $proforma->cliente->telefono }}</td>
                    <td class="label">Lugar de Entrega:</td>
                    <td>{{ $proforma->lugar_entrega }}</td>
                </tr>
                <tr>
                    <td class="label">Asunto:</td>
                    <td colspan="3">{{ $proforma->asunto }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Details Table -->
        <div class="info-section">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>ÍTEM</th>
                        <th>DESCRIPCIÓN</th>
                        <th>UM</th>
                        <th>CANTIDAD EN BRUTO Y SUELTO</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proforma->detalles as $index => $detalle)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="description">
                            <strong>{{ $detalle->descripcion }}</strong>
                            @if($detalle->incluye->count() > 0)
                            <div class="incluye-list">
                                <strong>INCLUYE:</strong>
                                <ul>
                                    @foreach($detalle->incluye as $item)
                                    <li>{{ $item->nombre }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </td>
                        <td>{{ $detalle->UM }}</td>
                        <td>{{ number_format($detalle->cantidad, 2) }}</td>
                        <td>$ {{ number_format($detalle->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Totals Section -->
        <div class="info-section">
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="label">SUB TOTAL:</td>
                        <td>US$</td>
                        <td style="text-align: right;">{{ number_format($proforma->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">DESCUENTO:</td>
                        <td>US$</td>
                        <td style="text-align: right;">{{ number_format($proforma->descuento, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">VALOR VENTA:</td>
                        <td>US$</td>
                        <td style="text-align: right;">{{ number_format($proforma->valor_venta, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">IGV:</td>
                        <td>US$</td>
                        <td style="text-align: right;">{{ number_format($proforma->igv, 2) }}</td>
                    </tr>
                    <tr class="total-final">
                        <td class="label">IMPORTE TOTAL:</td>
                        <td>US$</td>
                        <td style="text-align: right;">{{ number_format($proforma->importe_total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Terms and Conditions -->
        <div class="terms-section">
            
            <p><strong>TIEMPO DE ENTREGA:</strong> 03 A 05 DÍAS HÁBILES RECIBIDO LA O/C</p>
            <p><strong>VALIDEZ DE LA OFERTA:</strong> 07 DÍAS HÁBILES</p>
            
            <div style="margin-top: 20px;">
                <p><strong>Condiciones:</strong></p>
                <ul>
                    <li>Los precios incluyen IGV</li>
                    <li>Forma de Pago: {{ $proforma->forma_pago }}</li>
                    <li>Los precios están sujetos a variación sin previo aviso</li>
                    <li>Lugar de entrega: {{ $proforma->lugar_entrega }}</li>
                </ul>
            </div>
        </div>
        
        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Firma Usuario/Propill</div>
                <div>Gerente Comercial</div>
            </div>
            <div class="signature-box">
                <div>Firma Cliente/Usuario</div>
                <div>Gerente General</div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px; font-size: 11px;">
            <p>Elaborado por <strong>PRINTING</strong></p>
            <p>Aprobado por <strong>FABILLIOINTOP P.</strong></p>
        </div>
    </div>
    
    <!-- Demo Data (Remove in production) -->
    <script>
        // Demo data for preview - Remove this in production
        const demoData = {
            cliente: {
                nombre: "CONPHUNO S.A.C.",
                direccion: "AV. JAVIER PRADO ESTE 5621- CARAPERA LIMA",
                ruc: "20123456789",
                telefono: "(01) 4567890"
            },
            vendedor: {
                nombre: "ROBERTO",
                apellido: "GONZALES"
            },
            asunto: "SERVICIO DE ROLADO DE CODOS PARA STRUTS FEEDER",
            lugar_entrega: "FAULTONTOP",
            forma_pago: "CONTADO",
            moneda: "DOLARES AMERICANOS",
            subtotal: 1575.70,
            descuento: 0.00,
            valor_venta: 1575.70,
            igv: 283.03,
            importe_total: 1858.73,
            detalles: [
                {
                    descripcion: "SERVICIOS ROLADO DE CODOS PARA STRUTS FEEDER",
                    UM: "UND.",
                    cantidad: 1.00,
                    total: 1575.70,
                    incluye: [
                        "Fabricar de nuevo Bondeadora OMG6ORTH 114*60",
                        "Rolado de plancha",
                        "Soldadura de unión UTR 4920"
                    ]
                }
            ]
        };
        
        // Fill demo data
        document.querySelector('td:contains("{{ $proforma->cliente->nombre }}")').textContent = demoData.cliente.nombre;
    </script>
</body>
</html>