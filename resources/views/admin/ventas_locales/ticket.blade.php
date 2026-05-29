@php
    $pagos = $item->pagos ?? collect();
    $detalle = $item->detalle ?? collect();

    $clienteNombre = $item->nombre_cliente ?: 'Consumidor final';
    $clienteTelefono = $item->telefono_cliente ?: 'Sin teléfono';
    $cajero = $item->cajero?->nombre ?: 'Sin cajero';

    $metodosUsados = $pagos->pluck('metodo')
        ->filter()
        ->map(fn($m) => strtoupper($m))
        ->unique()
        ->implode(' + ');

    $metodosUsados = $metodosUsados ?: 'SIN PAGO';
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $item->numero_ticket }}</title>

    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .ticket {
            width: 80mm;
            padding: 8px;
            margin: 0 auto;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 6px;
        }

        .item {
            margin-bottom: 8px;
        }

        .item-name {
            font-weight: bold;
            word-break: break-word;
        }

        .small {
            font-size: 11px;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
            margin: 8px 0;
        }

        .empresa {
            font-size: 16px;
            font-weight: bold;
        }

        @media print {
            body {
                margin: 0;
            }

            .ticket {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="ticket">

        <div class="text-center">
            <div class="empresa">
                CORA  
            </div>

            <div>
                COMPROBANTE DE VENTA
            </div>

            <div class="small">
                Ticket #{{ $item->numero_ticket }}
            </div>

            <div class="small">
                Tel: 8779-0346
            </div>

            <div class="small">
                cora.store.cr@gmail.com
            </div>
        </div>

        <div class="line"></div>

        <div class="small">
            <div>
                <span class="bold">Fecha:</span>
                {{ optional($item->created_at)->format('d/m/Y') }}
            </div>

            <div>
                <span class="bold">Hora:</span>
                {{ optional($item->created_at)->format('H:i:s') }}
            </div>

            <div>
                <span class="bold">Cajero:</span>
                {{ $cajero }}
            </div>

            <div>
                <span class="bold">Cliente:</span>
                {{ $clienteNombre }}
            </div>

            <div>
                <span class="bold">Tel:</span>
                {{ $clienteTelefono }}
            </div>
        </div>

        <div class="line"></div>

        @forelse ($detalle as $d)

            <div class="item">

                <div class="item-name">
                    {{ $d->nombre_producto }}
                </div>

                <div class="row small">
                    <span>
                        {{ $d->cantidad }} x ₡{{ number_format((float) $d->precio_unitario, 2, '.', ',') }}
                    </span>

                    <span>
                        ₡{{ number_format((float) $d->total_linea, 2, '.', ',') }}
                    </span>
                </div>

                @if ($d->sku_snapshot)
                    <div class="small">
                        SKU: {{ $d->sku_snapshot }}
                    </div>
                @endif

            </div>

        @empty

            <div class="text-center small">
                No hay productos registrados.
            </div>

        @endforelse

        <div class="line"></div>

        <div class="row">
            <span>Artículos:</span>
            <span>{{ $detalle->sum('cantidad') }}</span>
        </div>

        <div class="row">
            <span>Subtotal:</span>
            <span>₡{{ number_format((float) $item->subtotal, 2, '.', ',') }}</span>
        </div>

        <div class="row">
            <span>Descuento:</span>
            <span>₡{{ number_format((float) $item->descuento, 2, '.', ',') }}</span>
        </div>

        <div class="line"></div>

        <div class="text-center total">
            TOTAL A PAGAR
            <br>
            ₡{{ number_format((float) $item->total, 2, '.', ',') }}
        </div>

        <div class="line"></div>

        <div class="small">

            <div class="bold">
                MÉTODO DE PAGO:
            </div>

            <div>
                {{ $metodosUsados }}
            </div>

            <br>

            @foreach ($pagos as $pago)

                <div class="row">
                    <span>{{ strtoupper($pago->metodo) }}</span>

                    <span>
                        ₡{{ number_format((float) $pago->monto, 2, '.', ',') }}
                    </span>
                </div>

                @if ($pago->referencia)
                    <div class="small">
                        Ref: {{ $pago->referencia }}
                    </div>
                @endif

            @endforeach

        </div>

        @if ($item->notas)

            <div class="line"></div>

            <div class="small">
                <span class="bold">NOTA:</span>
                {{ $item->notas }}
            </div>

        @endif

        <div class="line"></div>

        <div class="text-center small">
            <div>
                ¡GRACIAS POR SU COMPRA!
            </div>

            <div>
                Conserve este comprobante para cualquier cambio o reclamo.
            </div>

            <div>
                CORA  
            </div>
        </div>

    </div>

    <script>
        window.onload = () => {
            window.print();

            window.onafterprint = () => {
                window.close();
            };
        };
    </script>

</body>

</html>