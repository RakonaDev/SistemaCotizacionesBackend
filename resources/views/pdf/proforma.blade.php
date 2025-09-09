<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Proforma {{ $proforma->codigo }}</title>
  <style>
    * {
      font-family: 'DejaVu Sans', sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-size: 12px;
      color: #333;
      padding: 20px;
    }

    header {
      border-bottom: 3px solid #1E3A8A;
      margin-bottom: 20px;
      padding-bottom: 10px;
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .company {
      color: #1E3A8A;
      font-size: 20px;
      font-weight: bold;
    }

    .company-info {
      font-size: 11px;
      color: #374151;
      margin-top: 4px;
    }

    .proforma-title {
      font-size: 24px;
      font-weight: bold;
      color: #1D4ED8;
      text-transform: uppercase;
      text-align: right;
    }

    .info {
      margin: 20px 0;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    .info div {
      background: #F3F4F6;
      padding: 8px 12px;
      border-left: 4px solid #2563EB;
      font-size: 13px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table th {
      background: #1E40AF;
      color: #fff;
      text-align: left;
      padding: 8px;
      font-size: 12px;
    }

    table td {
      border-bottom: 1px solid #E5E7EB;
      padding: 6px;
      font-size: 12px;
    }

    .incluye {
      font-size: 11px;
      color: #2563EB;
      margin-top: 3px;
    }

    .incluye ul {
      margin: 3px 0 0 15px;
      padding: 0;
    }

    .incluye li {
      list-style-type: disc;
      font-size: 11px;
      color: #2563EB;
    }

    .totales {
      margin-top: 20px;
      width: 100%;
    }

    .totales td {
      padding: 6px;
      font-size: 13px;
    }

    .totales .label {
      text-align: right;
      font-weight: bold;
      color: #1E3A8A;
    }

    .totales .value {
      text-align: right;
      color: #111;
    }

    .firmas {
      margin-top: 50px;
      display: flex;
      flex-direction: row;
      gap: 10px;
      justify-content: space-between;
    }

    .firma {
      width: 45%;
      text-align: center;
      font-size: 12px;
    }

    .linea {
      border-top: 1px solid #000;
      margin-bottom: 5px;
      height: 40px;
    }

    .nombre {
      font-weight: bold;
      color: #1E3A8A;
    }

    .cargo {
      font-size: 11px;
      color: #374151;
    }

    footer {
      margin-top: 30px;
      text-align: center;
      font-size: 11px;
      color: #6B7280;
      border-top: 1px solid #E5E7EB;
      padding-top: 10px;
    }
  </style>
</head>

<body>
  <header>
    <div class="header-content">
      <div>
        <div class="company">LOGOS PERÚ S.A.C.</div>
        <div class="company-info">
          RUC: 12345678901 <br>
          Tel: (01) 987-654-321 <br>
          Email: contacto@logosperu.com
        </div>
      </div>
      <div class="proforma-title">Proforma</div>
    </div>
  </header>

  <section class="info">
    <div><strong>Código:</strong> {{ $proforma->codigo }}</div>
    <div><strong>Asunto:</strong> {{ $proforma->asunto }}</div>
    <div><strong>Cliente:</strong> {{ $proforma->cliente->nombre }}</div>
    <div><strong>Vendedor:</strong> {{ $proforma->vendedor->nombre }}</div>
    <div><strong>Fecha Inicio:</strong> {{ $proforma->fecha_inicial }}</div>
    <div><strong>Fecha Entrega:</strong> {{ $proforma->fecha_final }}</div>
    <div><strong>Días:</strong> {{ $proforma->dias }}</div>
    <div><strong>Moneda:</strong> {{ $proforma->moneda }}</div>
  </section>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Descripción</th>
        <th>UM</th>
        <th>Cantidad</th>
        <th>Precio Unit.</th>
        <th>Descuento</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($proforma->detalles as $index => $detalle)
      <tr>
        <td>{{ $index + 1 }}</td>
        <td>
          {{ $detalle->descripcion }}
          @if ($detalle->incluye->count() > 0)
          <div class="incluye">
            <strong>Incluye:</strong>
            <ul>
              @foreach ($detalle->incluye as $item)
              <li>{{ $item->nombre }}</li>
              @endforeach
            </ul>
          </div>
          @endif
        </td>
        <td>{{ $detalle->UM }}</td>
        <td>{{ $detalle->cantidad }}</td>
        <td>{{ number_format($detalle->precio_unit, 2) }}</td>
        <td>{{ number_format($detalle->descuento, 2) }}</td>
        <td>{{ number_format($detalle->total, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <table class="totales">
    <tr>
      <td class="label">Subtotal:</td>
      <td class="value">{{ number_format($proforma->subtotal, 2) }}</td>
    </tr>
    <tr>
      <td class="label">Descuento:</td>
      <td class="value">{{ number_format($proforma->descuento, 2) }}</td>
    </tr>
    <tr>
      <td class="label">Valor Venta:</td>
      <td class="value">{{ number_format($proforma->valor_venta, 2) }}</td>
    </tr>
    <tr>
      <td class="label">IGV:</td>
      <td class="value">{{ number_format($proforma->igv, 2) }}</td>
    </tr>
    <tr>
      <td class="label"><strong>Importe Total:</strong></td>
      <td class="value"><strong>{{ number_format($proforma->importe_total, 2) }}</strong></td>
    </tr>
  </table>

  <div class="firmas">
    <table style="width:100%; margin-top:50px; text-align:center;">
      <tr>
        <td style="width:50%;">
          <div class="linea"></div>
          <div class="nombre">Paul Llontop</div>
          <div class="cargo">Gerente Comercial</div>
          <div class="cargo">Elaborado por Ventas</div>
        </td>
        <td style="width:50%;">
          <div class="linea"></div>
          <div class="nombre">Paul Chapilliquen Roque</div>
          <div class="cargo">Gerente General</div>
          <div class="cargo">Aprobado por PAUL LLONTOP</div>
        </td>
      </tr>
    </table>
  </div>

  <footer>
    Documento generado automáticamente - {{ now()->format('d/m/Y') }}
  </footer>
</body>

</html>