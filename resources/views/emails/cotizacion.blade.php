<?php
/*
@component('mail::message')
# Cotización para {{ $cliente->nombre }}

Adjunto encontrarás la cotización solicitada.

**Descripción:** {{ $cotizacion->descripcion }}  
**Total:** S/. {{ number_format($cotizacion->monto_total, 2) }}  
**Fecha de entrega:** {{ \Carbon\Carbon::parse($cotizacion->fecha_final)->format('d/m/Y') }}

Gracias por confiar en nosotros.

@endcomponent
*/
?>

@component('mail::message')
# 🎯 Cotización N° {{ str_pad($cotizacion->id, 6, '0', STR_PAD_LEFT) }}

Estimado/a **{{ $cliente->nombre }}**,

Nos complace enviarte la cotización que solicitaste. Hemos preparado una propuesta detallada que esperamos sea de tu interés.

@component('mail::panel')
## 📋 Resumen de la Cotización

**Cotización N°:** {{ str_pad($cotizacion->id, 6, '0', STR_PAD_LEFT) }}  
**Descripción:** {{ $cotizacion->descripcion }}  
**Fecha de Inicio:** {{ \Carbon\Carbon::parse($cotizacion->fecha_inicial)->format('d/m/Y') }}  
**Fecha de Entrega:** {{ \Carbon\Carbon::parse($cotizacion->fecha_final)->format('d/m/Y') }}  
**Días de Entrega:** {{ $cotizacion->dias_entrega }} días  
@endcomponent

@component('mail::panel')
## 💰 **Monto Total: S/. {{ number_format($cotizacion->monto_total, 2) }}**
@endcomponent

@if(isset($detalles) && count($detalles) > 0)
## 🛠️ Servicios Incluidos

@foreach($detalles as $detalle)
### {{ $detalle['descripcion'] }}

@if(isset($detalle['servicios']) && count($detalle['servicios']) > 0)
@component('mail::table')
| Servicio | Subtotal |
|:---------|--------:|
@foreach($detalle['servicios'] as $servicio)
| {{ $servicio['descripcion'] }} | S/. {{ number_format($servicio['subtotal'], 2) }} |
@endforeach
| **Total de esta sección** | **S/. {{ number_format($detalle['precio_total'] ?? 0, 2) }}** |
@endcomponent

**Detalles financieros:**
- Costo Directo: S/. {{ number_format($detalle['costo_directo'] ?? 0, 2) }}
- Gastos Generales ({{ $detalle['gg'] ?? 0 }}%): S/. {{ number_format(($detalle['costo_directo'] ?? 0) * (($detalle['gg'] ?? 0) / 100), 2) }}
- Utilidad ({{ $detalle['utilidad'] ?? 0 }}%): S/. {{ number_format(($detalle['costo_directo'] ?? 0) * (($detalle['utilidad'] ?? 0) / 100), 2) }}

---
@endif
@endforeach
@endif

## 📎 Información del Adjunto

El PDF adjunto contiene todos los detalles técnicos y financieros de la cotización. Te recomendamos revisarlo cuidadosamente.

@component('mail::button', ['url' => 'mailto:contacto@tuempresa.com?subject=Consulta sobre Cotización ' . str_pad($cotizacion->id, 6, '0', STR_PAD_LEFT)])
Contactar Ahora
@endcomponent

@component('mail::panel')
## ⚠️ Información Importante

- ✅ **Validez:** Esta cotización tiene una validez de **30 días calendario**
- 💳 **Precios:** Los precios incluyen IGV (18%)
- 💰 **Adelanto:** Se requiere un adelanto del 50% para iniciar los trabajos
- 📅 **Tiempo:** El tiempo de entrega se cuenta desde la confirmación del pedido
- 🔄 **Modificaciones:** Cualquier cambio al proyecto original será cotizado por separado
@endcomponent

## 📞 ¿Tienes Preguntas?

Si necesitas aclaraciones sobre algún punto de la cotización o deseas realizar modificaciones, no dudes en contactarnos:

- **Email:** contacto@tuempresa.com
- **Teléfono:** +51 999 999 999
- **Horario:** Lunes a Viernes de 8:00 AM a 6:00 PM

## 🤝 Próximos Pasos

Para confirmar el proyecto:

1. **Revisa** el PDF adjunto con todos los detalles
2. **Responde** a este email con tu confirmación
3. **Coordina** el adelanto del 50% para iniciar
4. **Programa** la fecha de inicio según tu disponibilidad

---

**¡Gracias por confiar en nosotros!** 🙏

Estamos emocionados por la posibilidad de trabajar contigo en este proyecto. Tu confianza es muy importante para nosotros.

Saludos cordiales,  
**El Equipo de Tu Empresa**

@component('mail::subcopy')
Esta cotización fue generada automáticamente el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}.  
Si tienes problemas para ver este email, contacta a nuestro soporte técnico.
@endcomponent

@endcomponent