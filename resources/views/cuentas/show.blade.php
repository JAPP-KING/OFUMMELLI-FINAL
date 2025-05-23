<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Detalle de Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div style="color: white;" class="bg-white dark:bg-gray-800 p-6 rounded shadow">

                <!-- Información General -->
                <p class="mb-2 text-light-text dark:text-dark-text"><strong>Cajera:</strong> {{ Auth::user()->name }}</p>
                <p class="mb-2 text-light-text dark:text-dark-text"><strong>Barrero:</strong> {{ $cuenta->barrero }}</p>
                <p class="mb-2 text-light-text dark:text-dark-text"><strong>Responsable:</strong> {{ $cuenta->responsable_pedido ?? 'No especificado' }}</p>
                <p class="mb-2 text-light-text dark:text-dark-text"><strong>Cliente:</strong> {{ $cuenta->cliente_nombre ?? 'No especificado' }}</p>
                <p class="mb-2 text-light-text dark:text-dark-text"><strong>Estación:</strong> {{ $cuenta->estacion }}</p>
                <p class="mb-4 text-light-text dark:text-dark-text"><strong>Fecha:</strong> {{ $cuenta->fecha_apertura->format('d/m/Y h:i A') }}</p>
                <p class="text-green-400 text-sm"><strong>Tasa Usada:</strong> {{ number_format($cuenta->tasa_dia, 2) }} Bs/USD</p>

                <!-- Productos -->
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Pedido:</h3>
                <table class="w-full border mb-4">
                    <thead class="bg-gray-700 text-white">
                        <tr>
                            <th class="px-4 py-2 border">Producto</th>
                            <th class="px-4 py-2 border">Cantidad</th>
                            <th class="px-4 py-2 border">Precio</th>
                            <th class="px-4 py-2 border">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($cuenta->productos as $item)
                        @php
                            $producto = $productos[$item['producto_id']] ?? null;
                        @endphp
                        <tr>
                            <td class="text-light-text dark:text-dark-text border px-4 py-2">{{ $producto->nombre ?? 'Desconocido' }}</td>
                            <td class="text-light-text dark:text-dark-text border px-4 py-2">{{ $item['cantidad'] }}</td>
                            <td class="text-light-text dark:text-dark-text border px-4 py-2">${{ number_format($item['precio'] ?? 0, 2) }}</td>
                            <td class="text-light-text dark:text-dark-text border px-4 py-2">${{ number_format($item['subtotal'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <p class="font-bold text-green-400 mb-6">Total Estimado: ${{ number_format($cuenta->total_estimado, 2) }}</p>

                @php
    // Cálculo del total pagado y las propinas por separado
    $totalPagadoEnDolares = 0;
    $propinasEnDolares = 0;

    foreach ($cuenta->metodos_pago as $pago) {
        $monto = $pago['monto'] ?? 0;

        if (in_array($pago['metodo'], ['divisas', 'zelle', 'tarjeta_credito_dolares'])) {
            $totalPagadoEnDolares += $monto; // Pagos en dólares
        } elseif (in_array($pago['metodo'], ['pago_movil', 'bs_efectivo', 'debito', 'punto_venta', 'tarjeta_credito_bolivares'])) {
            $totalPagadoEnDolares += $monto / $cuenta->tasa_dia; // Convertir bolívares a dólares
        } elseif ($pago['metodo'] === 'euros') {
            $totalPagadoEnDolares += $monto * 1.1; // Convertir euros a dólares (tasa fija)
        }

        // Separar las propinas
        if ($pago['metodo'] === 'propina_divisas') {
            $propinasEnDolares += $monto; // Propinas en dólares
        } elseif ($pago['metodo'] === 'propina_bolivares') {
            $propinasEnDolares += $monto / $cuenta->tasa_dia; // Propinas en bolívares convertidas a dólares
        }
    }

    // Excluir propinas del cálculo del vuelto
    $pagosSinPropinas = $totalPagadoEnDolares - $propinasEnDolares; // Total pagado menos propinas
    $vuelto = max(0, $pagosSinPropinas - $cuenta->total_estimado);
@endphp

                <div class="mb-4">
                    <p class="text-light-text dark:text-dark-text">
                        <strong>Total Pagado:</strong> ${{ number_format($totalPagadoEnDolares, 2) }}
                    </p>
                        <br>
                    <p class="text-light-text dark:text-dark-text">
                        <strong>Propinas:</strong> ${{ number_format($propinasEnDolares, 2) }}
                    </p>
                        <br>
                    <p class="text-light-danger dark:text-dark-danger">
                        <strong>Vuelto:</strong> ${{ number_format($vuelto, 2) }}
                    </p>
                </div>

                <!-- Métodos de Pago -->
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Métodos de Pago:</h3>
                <ul class="list-disc pl-6">
                    @foreach ($cuenta->metodos_pago as $pago)
                        <li class="mb-1 text-light-text dark:text-dark-text">
                        @php
                            $nombreMetodo = match($pago['metodo']) {
                                'divisas' => 'Divisas ($)',
                                'pago_movil' => 'Pago Móvil (Bs)',
                                'zelle' => 'Zelle (Dólares)',
                                'punto_venta' => 'Punto de Venta',
                                'tarjeta_credito_dolares' => 'Tarjeta de Crédito (Dólares)',
                                'tarjeta_credito_bolivares' => 'Tarjeta de Crédito (Bolívares)',
                                'bs_efectivo' => 'Bolívares en Efectivo',
                                'debito' => 'Tarjeta Débito',
                                'euros' => 'Euros en Efectivo',
                                'cuenta_casa' => 'Cuenta Por la Casa',
                                'propina_divisas' => 'Propina (Divisas)',
                                'propina_bolivares' => 'Propina (Bolívares)',
                                default => 'Método Desconocido',
                            };

                            $simbolo = match($pago['metodo']) {
                                'divisas', 'zelle', 'tarjeta_credito_dolares', 'propina_divisas' => '$',
                                'bs_efectivo', 'debito', 'punto_venta', 'tarjeta_credito_bolivares', 'pago_movil', 'propina_bolivares' => 'Bs ',
                                'euros' => '€ ',
                                default => '',
                            };
                        @endphp

                        <strong>{{ $nombreMetodo }}:</strong>
                            {{ $simbolo }}{{ number_format($pago['monto'], 2) }}
                            @if (!empty($pago['referencia']))
                                <span class="ml-2 text-sm text-gray-500">(Ref: {{ $pago['referencia'] }})</span>
                            @endif
                            @if ($pago['metodo'] === 'pago_movil')
                                <span class="ml-2 text-sm text-gray-500">(Cuenta: {{ ucfirst(str_replace('_', ' ', $pago['cuenta'] ?? 'No especificado')) }})</span>
                            @endif
                            @if ($pago['metodo'] === 'punto_venta')
                                <span class="ml-2 text-sm text-gray-500">(Banco: {{ ucfirst($pago['banco'] ?? 'No especificado') }})</span>
                            @endif
                            @if ($pago['metodo'] === 'cuenta_casa')
                                <span class="ml-2 text-sm text-gray-500">(Autorizado por: {{ $pago['autorizado_por'] ?? 'No especificado' }})</span>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <!-- Botón de regreso -->
                <div class="mt-6 flex justify-end gap-4">
                    <a href="{{ route('cuentas.index') }}"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition-all duration-200">
                        Volver
                    </a>

                    @if (!$cuenta->pagada)
                        <form method="POST" action="{{ route('cuentas.marcarPagada', $cuenta) }}" class="inline-block">
                            @csrf
                            <button type="submit"
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg shadow hover:bg-green-700 transition-all duration-200"
                                    onclick="return confirm('¿Marcar esta cuenta como pagada?')">
                                Marcar como Pagada
                            </button>
                        </form>
                    @else
                        <span class="text-green-400 font-semibold ml-4">Cuenta ya pagada</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>