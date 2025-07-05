<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <title>Cotización PDF - {{ $cotizacion->id }}</title>
  <style>
    :root {
      --primary: #0B2447;
      --secondary: #19376D;
      --terciary: #576CBC;
      --quaternary: #A5D7E8;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Arial', sans-serif;
      font-size: 12px;
      line-height: 1.4;
      color: #333;
      background-color: #fff;
    }

    .container {
      max-width: 100%;
      margin: 0 auto;
      padding: 20px;
    }

    /* Header */
    .header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: white;
      padding: 25px;
      margin-bottom: 30px;
      border-radius: 8px;
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .company-info h1 {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .company-info p {
      font-size: 14px;
      opacity: 0.9;
    }

    .quote-number {
      text-align: right;
    }

    .quote-number h2 {
      font-size: 24px;
      margin-bottom: 5px;
    }

    .quote-number p {
      font-size: 14px;
      opacity: 0.9;
    }

    /* Client and Quote Info */
    .info-section {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
      gap: 30px;
    }

    .info-box {
      flex: 1;
      background-color: #f8f9fa;
      border: 2px solid var(--quaternary);
      border-radius: 8px;
      padding: 20px;
    }

    .info-box h3 {
      color: var(--primary);
      font-size: 16px;
      margin-bottom: 15px;
      border-bottom: 2px solid var(--terciary);
      padding-bottom: 5px;
    }

    .info-row {
      display: flex;
      margin-bottom: 8px;
    }

    .info-label {
      font-weight: bold;
      color: var(--secondary);
      width: 120px;
      flex-shrink: 0;
    }

    .info-value {
      color: #333;
    }

    /* Summary Box */
    .summary-box {
      background: linear-gradient(135deg, var(--terciary) 0%, var(--quaternary) 100%);
      color: var(--primary);
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 30px;
      text-align: center;
    }

    .summary-box h3 {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .total-amount {
      font-size: 32px;
      font-weight: bold;
      color: var(--primary);
    }

    /* Cotizaciones Details */
    .cotizacion-section {
      margin-bottom: 40px;
      page-break-inside: avoid;
    }

    .cotizacion-header {
      background-color: var(--secondary);
      color: white;
      padding: 15px 20px;
      border-radius: 8px 8px 0 0;
      margin-bottom: 0;
    }

    .cotizacion-header h3 {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .cotizacion-meta {
      display: flex;
      justify-content: space-between;
      font-size: 11px;
      opacity: 0.9;
    }

    .services-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .services-table thead {
      background-color: var(--terciary);
      color: white;
    }

    .services-table th {
      padding: 12px 15px;
      text-align: left;
      font-weight: bold;
      font-size: 11px;
      text-transform: uppercase;
    }

    .services-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #e9ecef;
      vertical-align: top;
    }

    .services-table tbody tr:hover {
      background-color: #f8f9fa;
    }

    .services-table tbody tr:nth-child(even) {
      background-color: #f8f9fa;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .font-bold {
      font-weight: bold;
    }

    .price {
      color: var(--primary);
      font-weight: bold;
    }

    /* Costs Summary */
    .costs-summary {
      background-color: #f8f9fa;
      border: 2px solid var(--quaternary);
      border-radius: 8px;
      padding: 15px;
      margin-top: 15px;
    }

    .costs-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      padding: 5px 0;
    }

    .costs-row.total {
      border-top: 2px solid var(--terciary);
      margin-top: 10px;
      padding-top: 10px;
      font-weight: bold;
      font-size: 14px;
      color: var(--primary);
    }

    /* Footer */
    .footer {
      margin-top: 50px;
      padding-top: 20px;
      border-top: 2px solid var(--quaternary);
      text-align: center;
      color: #666;
      font-size: 10px;
    }

    .terms {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      margin-top: 30px;
      border-left: 4px solid var(--terciary);
    }

    .terms h4 {
      color: var(--primary);
      margin-bottom: 10px;
      font-size: 14px;
    }

    .terms ul {
      list-style-type: disc;
      margin-left: 20px;
    }

    .terms li {
      margin-bottom: 5px;
      font-size: 11px;
    }

    /* Page break utilities */
    .page-break {
      page-break-before: always;
    }

    @media print {
      .container {
        padding: 10px;
      }

      .cotizacion-section {
        page-break-inside: avoid;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <div class="header-content">
        <div class="company-info">
          <h1>Tu Empresa</h1>
          <p>Dirección de tu empresa</p>
          <p>Teléfono: +51 999 999 999 | Email: contacto@tuempresa.com</p>
        </div>
        <div class="quote-number">
          <h2>COTIZACIÓN</h2>
          <p>N° {{ str_pad($cotizacion->id, 6, '0', STR_PAD_LEFT) }}</p>
          <p>Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>
      </div>
    </div>

    <!-- Client and Quote Information -->
    <div class="info-section">
      <div class="info-box">
        <h3>Información del Cliente</h3>
        <div class="info-row">
          <span class="info-label">Cliente:</span>
          <span class="info-value">{{ $cliente->nombre }}</span>
        </div>
        @if(isset($cliente->correo))
        <div class="info-row">
          <span class="info-label">Email:</span>
          <span class="info-value">{{ $cliente->correo }}</span>
        </div>
        @endif
        @if(isset($cliente->telefono))
        <div class="info-row">
          <span class="info-label">Teléfono:</span>
          <span class="info-value">{{ $cliente->telefono }}</span>
        </div>
        @endif
        @if(isset($cliente->direccion))
        <div class="info-row">
          <span class="info-label">Dirección:</span>
          <span class="info-value">{{ $cliente->direccion }}</span>
        </div>
        @endif
      </div>

      <div class="info-box">
        <h3>Detalles de la Cotización</h3>
        <div class="info-row">
          <span class="info-label">Descripción:</span>
          <span class="info-value">{{ $cotizacion->descripcion }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Fecha Inicial:</span>
          <span class="info-value">{{ \Carbon\Carbon::parse($cotizacion->fecha_inicial)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Fecha Final:</span>
          <span class="info-value">{{ \Carbon\Carbon::parse($cotizacion->fecha_final)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Días de Entrega:</span>
          <span class="info-value">{{ $cotizacion->dias_entrega }} días</span>
        </div>
      </div>
    </div>

    <!-- Total Summary -->
    <div class="summary-box">
      <h3>Monto Total de la Cotización</h3>
      <div class="total-amount">S/. {{ number_format($cotizacion->monto_total, 2) }}</div>
    </div>

    <!-- Detailed Cotizaciones -->
    @foreach ($detalles as $index => $cot)
    <div class="cotizacion-section">
      <div class="cotizacion-header">
        <h3>{{ $cot['descripcion'] }}</h3>
        <div class="cotizacion-meta">
          <span>Cantidad: {{ $cot['cantidad'] ?? 1 }}</span>
          <span>Costo Directo: S/. {{ number_format($cot['costo_directo'] ?? 0, 2) }}</span>
          <span>G.G.: {{ ($cot['gg'] ?? 0) }}%</span>
          <span>Utilidad: {{ ($cot['utilidad'] ?? 0) }}%</span>
        </div>
      </div>

      <table class="services-table">
        <thead>
          <tr>
            <th style="width: 50%">Descripción del Servicio</th>
            <th style="width: 15%" class="text-center">Cantidad</th>
            <th style="width: 15%" class="text-right">Precio Unit.</th>
            <th style="width: 20%" class="text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($cot['servicios'] as $servicio)
          <tr>
            <td>
              <strong>{{ $servicio['descripcion'] }}</strong>
              @if(isset($servicio['tipo']))
              <br><small style="color: #666;">Tipo: {{ $servicio['tipo'] }}</small>
              @endif
              @if(isset($servicio['horas']) && $servicio['tipo'] === 'AREA')
              <br><small style="color: #666;">Horas hábiles: {{ $servicio['horas'] }}</small>
              @endif
            </td>
            <td class="text-center">
              @if($servicio['tipo'] !== 'AREA')
              {{ $servicio['cantidad'] ?? 1 }}
              @else
              {{ $servicio['horas'] ?? 0 }} hrs
              @endif
            </td>
            <td class="text-right">
              @if($servicio['tipo'] !== 'AREA')
              S/. {{ number_format($servicio['precio_unit'] ?? 0, 2) }}
              @else
              S/. {{ number_format($servicio['costo'] ?? 0, 2) }}/hr
              @endif
            </td>
            <td class="text-right price">
              S/. {{ number_format($servicio['subtotal'], 2) }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Costs Summary for this cotization -->
      <div class="costs-summary">
        <div class="costs-row">
          <span>Costo Directo:</span>
          <span>S/. {{ number_format($cot['costo_directo'] ?? 0, 2) }}</span>
        </div>
        <div class="costs-row">
          <span>Gastos Generales ({{ ($cot['gg'] ?? 0) }}%):</span>
          <span>S/. {{ number_format(($cot['costo_directo'] ?? 0) * (($cot['gg'] ?? 0) / 100), 2) }}</span>
        </div>
        <div class="costs-row">
          <span>Utilidad ({{ ($cot['utilidad'] ?? 0) }}%):</span>
          <span>S/. {{ number_format(($cot['costo_directo'] ?? 0) * (($cot['utilidad'] ?? 0) / 100), 2) }}</span>
        </div>
        <div class="costs-row total">
          <span>Total de esta sección:</span>
          <span>S/. {{ number_format($cot['precio_total'] ?? 0, 2) }}</span>
        </div>
      </div>
    </div>
    @endforeach

    <!-- Terms and Conditions -->
    <div class="terms">
      <h4>Términos y Condiciones</h4>
      <ul>
        <li>Esta cotización tiene una validez de 30 días calendario.</li>
        <li>Los precios incluyen IGV (18%).</li>
        <li>El tiempo de entrega se cuenta a partir de la confirmación del pedido.</li>
        <li>Se requiere un adelanto del 50% para iniciar los trabajos.</li>
        <li>Cualquier modificación al proyecto original será cotizada por separado.</li>
        <li>Los materiales y equipos quedan bajo responsabilidad del cliente una vez entregados.</li>
      </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>Esta cotización fue generada automáticamente el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
      <p>Para cualquier consulta, no dude en contactarnos.</p>
      <p style="margin-top: 10px; color: var(--primary); font-weight: bold;">¡Gracias por confiar en nosotros!</p>
    </div>
  </div>
</body>

</html>