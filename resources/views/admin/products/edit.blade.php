<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Producto') }}
            </h2>
            <a href="{{ route('products.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Producto *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sku') border-red-500 @enderror">
                                @error('sku')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Stock -->
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700">Stock *</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stock') border-red-500 @enderror">
                                @error('stock')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Marca -->
                            <div>
                                <label for="brand_id" class="block text-sm font-medium text-gray-700">Marca</label>
                                <select name="brand_id" id="brand_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('brand_id') border-red-500 @enderror">
                                    <option value="">Seleccione una marca</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) ==$brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Impuesto -->
                            <div>
                                <label for="tax_id" class="block text-sm font-medium text-gray-700">Impuesto</label>
                                <select name="tax_id" id="tax_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('tax_id') border-red-500 @enderror">
                                    @foreach($taxes as $tax)
                                        <option value="{{ $tax->id }}" {{ old('tax_id', $product->tax_id ?? 1) == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->name }} ({{ number_format($tax->rate, 2) }}%)
                                        </option>
                                    @endforeach
                                </select>
                                @error('tax_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Categorías -->
                            <div class="md:col-span-2">
                                <label for="categories" class="block text-sm font-medium text-gray-700">Categorías</label>
                                @php
                                    $selectedCategories = old('categories',$product->categories->pluck('id')->toArray()) ?? [];
                                @endphp
                                <select name="categories[]" id="categories" multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ in_array($category->id,$selectedCategories) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Mantén presionado Ctrl (o Cmd) para seleccionar varias.</p>
                            </div>

                            <!-- Precio -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Precio *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="price" id="price"
                                        value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                                        class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('price') border-red-500 @enderror">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Precio de comparación -->
                            <div>
                                <label for="compare_price" class="block text-sm font-medium text-gray-700">Precio Anterior</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="compare_price" id="compare_price"
                                        value="{{ old('compare_price', $product->compare_price) }}" step="0.01" min="0"
                                        class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('compare_price') border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Para mostrar precio original con descuento</p>
                                @error('compare_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea name="description" id="description" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Imagen actual -->
                            @if($product->image)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Imagen Principal Actual</label>
                                    <img src="{{ Storage::disk('r2')->url($product->image) }}" alt="{{ $product->name }}"
                                        class="h-32 w-32 object-cover rounded-lg border border-gray-200 shadow-sm">
                                </div>
                            @endif

                            <!-- Imagen Principal -->
                            <div class="md:col-span-2">
                                <label for="image" class="block text-sm font-medium text-gray-700">
                                    {{ $product->image ? 'Cambiar Imagen Principal' : 'Imagen Principal' }}
                                </label>
                                <input type="file" name="image" id="image" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('image') border-red-500 @enderror">
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Galería Existente y Nuevas Imágenes -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Galería de Imágenes</label>
                                @if($product->images &&$product->images->count() > 0)
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4">
                                        @foreach($product->images as $img)
                                            <div class="relative group" id="image-{{ $img->id }}">
                                                <img src="{{ Storage::disk('r2')->url($img->image) }}"
                                                    class="h-24 w-full object-cover rounded-lg border border-gray-200 shadow-sm">
                                                <button type="button" onclick="deleteProductImage({{ $img->id }})"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition opacity-0 group-hover:opacity-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <label for="images" class="block text-sm font-medium text-gray-700">Agregar más imágenes a la galería</label>
                                <input type="file" name="images[]" id="images" accept="image/*" multiple
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Puedes seleccionar varias imágenes simultáneamente.</p>
                            </div>

                           <!-- Sección Documentos PDF -->
                                <div class="md:col-span-2 border-t pt-6 mt-2">
                                    <h3 class="text-md font-semibold text-gray-800 mb-3">Documento PDF (Ficha Técnica / Manual)</h3>
                                    
                                    <!-- Lista de PDFs Existentes -->
                                    @if($product->pdfs && $product->pdfs->count() > 0)
                                        <div class="space-y-2 mb-4">
                                            @foreach($product->pdfs as $pdf)
                                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200" id="pdf-{{ $pdf->id }}">
                                                    <div class="flex items-center gap-3 overflow-hidden">
                                                        <svg class="w-8 h-8 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <div class="truncate">
                                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $pdf->title }}</p>
                                                            <a href="{{ Storage::disk('r2')->url($pdf->file_path) }}" target="_blank" class="text-xs text-indigo-600 hover:underline">Ver archivo</a>
                                                        </div>
                                                    </div>
                                                    <button type="button" onclick="deleteProductPdf({{ $pdf->id }})" class="text-red-500 hover:text-red-700 text-sm font-semibold">
                                                        Eliminar
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <label for="pdf_file" class="block text-sm font-medium text-gray-700">Subir nuevo PDF</label>
                                    <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('pdf_file') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Formato admitido: PDF (Máx. 10MB)</p>
                                    @error('pdf_file')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                            <!-- Sección Video -->
<div class="md:col-span-2 border-t pt-6">
    <h3 class="text-md font-semibold text-gray-800 mb-3">Gestión de Video</h3>
    
    <!-- Lista de Vídeos Guardados -->
    @if($product->videos && $product->videos->count() > 0)
        <div class="mb-4 space-y-3">
            <label class="block text-sm font-medium text-gray-700">Videos Cargados</label>
            @foreach($product->videos as $video)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200" id="video-{{ $video->id }}">
                    <div class="truncate">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $video->youtube_url }}</p>
                        @if($video->video_id)
                            <p class="text-xs text-gray-500">ID de YouTube: {{ $video->video_id }}</p>
                        @endif
                    </div>
                    <button type="button" onclick="deleteProductVideo({{ $video->id }})" class="text-red-500 hover:text-red-700 text-sm font-semibold">
                        Eliminar
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Video URL -->
        <div class="md:col-span-2">
            <label for="video_url" class="block text-sm font-medium text-gray-700">Agregar enlace de Video (YouTube / Vimeo)</label>
            <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}"
                placeholder="https://www.youtube.com/watch?v=..."
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('video_url') border-red-500 @enderror">
            @error('video_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
                            <!-- Checkboxes -->
                            <div class="md:col-span-2 space-y-4 pt-2 border-t">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm font-medium text-gray-900">
                                        Producto Activo
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_featured" class="ml-2 block text-sm font-medium text-gray-900">
                                        Producto Destacado
                                    </label>
                                </div>
                            </div>

                            <!-- SEO Settings -->
                            <div class="md:col-span-2 border-t pt-6 mt-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración SEO</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                                        <input type="text" name="meta_title" id="meta_title"
                                            value="{{ old('meta_title', $product->meta_title) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('meta_title') border-red-500 @enderror"
                                            placeholder="Título para buscadores (opcional)">
                                        @error('meta_title')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                                        <textarea name="meta_description" id="meta_description" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('meta_description') border-red-500 @enderror"
                                            placeholder="Descripción para buscadores (opcional)">{{ old('meta_description', $product->meta_description) }}</textarea>
                                        @error('meta_description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-6 flex items-center justify-end gap-x-4 border-t pt-6">
                            <a href="{{ route('products.index') }}" class="text-sm font-semibold leading-6 text-gray-700 hover:text-gray-900">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-sm">
                                Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteProductImage(imageId) {
            if (!confirm('¿Estás seguro de que deseas eliminar esta imagen?')) return;

            fetch(`/admin/products/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const el = document.getElementById(`image-${imageId}`);
                    if (el) el.remove();
                }
            });
        }

        function deleteProductVideo(videoId) {
    if (!confirm('¿Estás seguro de eliminar este video?')) return;

    fetch(`/admin/products/videos/${videoId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById(`video-${videoId}`);
            if (el) el.remove();
        }
    });
}

function deleteProductPdf(pdfId) {
    if (!confirm('¿Estás seguro de eliminar este PDF?')) return;

    fetch(`/admin/products/pdfs/${pdfId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById(`pdf-${pdfId}`);
            if (el) el.remove();
        }
    });
}
    </script>
</x-app-layout>