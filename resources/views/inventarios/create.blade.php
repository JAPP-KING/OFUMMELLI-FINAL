<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            {{ __('Crear Nuevo Producto') }}
        </h2>
    </x-slot>

    <div class="py-6 bg-gray-100 dark:bg-gray-900">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('inventarios.store') }}" method="POST">
                    @csrf

                    <!-- Nombre del Producto -->
                    <div class="mb-4">
                        <label for="nombre" class="block text-gray-800 dark:text-gray-200">Nombre del Producto:</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                               class="w-full border rounded px-4 py-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Código del Producto -->
                    <div class="mb-4">
                        <label for="codigo" class="block text-gray-800 dark:text-gray-200">Código:</label>
                        <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}" required
                               class="w-full border rounded px-4 py-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Cantidad Inicial -->
                    <div class="mb-4">
                        <label for="cantidad_inicial" class="block text-gray-800 dark:text-gray-200">Cantidad Inicial:</label>
                        <input type="number" name="cantidad_inicial" id="cantidad_inicial" value="{{ old('cantidad_inicial', 0) }}" required min="0"
                               class="w-full border rounded px-4 py-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Precio Costo -->
                    <div class="mb-4">
                        <label for="precio_costo" class="block text-gray-800 dark:text-gray-200">Precio Costo:</label>
                        <input type="number" name="precio_costo" id="precio_costo" value="{{ old('precio_costo', 0) }}" step="0.01" required min="0"
                               class="w-full border rounded px-4 py-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
        <label for="unidad_medida">Unidad de Medida</label>
        <input type="text" name="unidad_medida" id="unidad_medida" required>
    </div>
    <button type="submit">Guardar Producto</button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>