<x-front-layout>
    @section('title', $product->meta_title ?: $product->name . ' - ' . config('app.name'))
    @section('meta_description', $product->meta_description ?: Str::limit(strip_tags($product->description), 160))

    <div class="ap-breadcrumb-container">
        <x-breadcrumb :items="[
            ['label' => 'Productos', 'url' => route('shop.index')],
            ['label' => $product->category->name ?? 'Sin Categoría', 'url' => $product->category ? route('shop.byCategory', $product->category->slug) : null],
            ['label' => $product->name]
        ]" />
    </div>

    {{-- Layout del Detalle --}}
    <div class="ap-detail-layout">

        {{-- =========================================================
             GALERÍA DE IMÁGENES Y VIDEO
        ========================================================== --}}
        <div
            style="min-width: 0; width: 100%;"
            x-data="{
                activeMediaType: 'image',
                activeMediaUrl: '{{ $product->image ? Storage::disk('r2')->url($product->image) : asset('images/no-image.png') }}'
            }"
        >

            {{-- =====================================================
                 VISUALIZADOR PRINCIPAL
            ====================================================== --}}
            <div class="ap-gallery__main-wrapper">

                {{-- =========================
                     VISOR DE IMAGEN
                ========================== --}}
                <template x-if="activeMediaType === 'image'">
                    <div
                        x-data="{
                            zoom: false,
                            x: 0,
                            y: 0
                        }"
                        @mousemove="
                            x = ($event.offsetX / $event.target.offsetWidth) * 100;
                            y = ($event.offsetY / $event.target.offsetHeight) * 100;
                        "
                        @mouseenter="zoom = true"
                        @mouseleave="zoom = false"
                        style="width: 100%; overflow: hidden;"
                    >
                        <img
                            :src="activeMediaUrl"
                            class="ap-gallery__main"
                            :style="
                                zoom
                                ? `transform: scale(2); transform-origin: ${x}% ${y}%;`
                                : ''
                            "
                            alt="{{ $product->name }}"
                        >
                    </div>
                </template>


                {{-- =========================
                     VISOR DE VIDEO YOUTUBE / VIMEO
                ========================== --}}
                <template x-if="activeMediaType === 'video_url'">
                    <div
                        style="
                            position: relative;
                            width: 100%;
                            aspect-ratio: 16 / 9;
                            background: #000;
                            border-radius: 8px;
                            overflow: hidden;
                            min-height: 300px;
                        "
                    >
                        <iframe
                            :src="activeMediaUrl"
                            title="Video de {{ $product->name }}"
                            frameborder="0"
                            allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
                            allowfullscreen
                            style="
                                position: absolute;
                                inset: 0;
                                width: 100%;
                                height: 100%;
                                min-height: 300px;
                                display: block;
                                border: 0;
                                z-index: 20;
                                background: #000;
                            "
                        ></iframe>
                    </div>
                </template>


                {{-- =========================
                     VISOR DE VIDEO HTML5
                ========================== --}}
                <template x-if="activeMediaType === 'video_file'">
                    <div
                        style="
                            width: 100%;
                            aspect-ratio: 16 / 9;
                            border-radius: 8px;
                            overflow: hidden;
                            background: #000;
                        "
                    >
                        <video
                            controls
                            autoplay
                            playsinline
                            style="
                                width: 100%;
                                height: 100%;
                                display: block;
                                object-fit: contain;
                                background: #000;
                            "
                        >
                            <source :src="activeMediaUrl" type="video/mp4">

                            Tu navegador no soporta el reproductor de video.
                        </video>
                    </div>
                </template>

            </div>


            {{-- =====================================================
                 SLIDER DE MINIATURAS
            ====================================================== --}}
            @php
                $hasVideos = $product->videos && $product->videos->count() > 0;
                $hasImages = $product->images && $product->images->count() > 0;
            @endphp

            @if($hasImages || $hasVideos)

                <div
                    class="thumb-slider-container"
                    style="
                        margin-top: 12px;
                        width: 100%;
                        max-width: 100%;
                        overflow: hidden;
                        position: relative;
                    "
                >

                    <div
                        class="swiper thumbSwiper"
                        style="
                            width: 100%;
                            height: 80px;
                            overflow: hidden;
                        "
                    >

                        <div
                            class="swiper-wrapper"
                            style="box-sizing: border-box;"
                        >

                            {{-- =================================================
                                 MINIATURA IMAGEN PRINCIPAL
                            ================================================== --}}
                            <div
                                class="swiper-slide"
                                style="height: 100%;"
                            >

                                <div
                                    class="thumb-item active"
                                    style="
                                        height: 100%;
                                        width: 100%;
                                        overflow: hidden;
                                        cursor: pointer;
                                        border-radius: 6px;
                                    "
                                    @click="
                                        activeMediaType = 'image';
                                        activeMediaUrl = '{{ $product->image ? Storage::disk('r2')->url($product->image) : asset('images/no-image.png') }}';

                                        $el
                                            .closest('.swiper-wrapper')
                                            .querySelectorAll('.thumb-item')
                                            .forEach(e => e.classList.remove('active'));

                                        $el.classList.add('active');
                                    "
                                >

                                    <img
                                        src="{{ $product->image ? Storage::disk('r2')->url($product->image) : asset('images/no-image.png') }}"
                                        alt="Principal"
                                        style="
                                            width: 100%;
                                            height: 100%;
                                            object-fit: cover;
                                            display: block;
                                        "
                                    >

                                </div>

                            </div>


                            {{-- =================================================
                                 MINIATURAS DE VIDEOS
                            ================================================== --}}
                            @if($hasVideos)

                                @foreach($product->videos as $video)

                                    @php

                                        /*
                                         * Construimos la URL de embed
                                         * para YouTube o Vimeo.
                                         */

                                        $embedUrl = '';

                                        if ($video->video_id) {

                                            $embedUrl =
                                                'https://www.youtube.com/embed/'
                                                . $video->video_id
                                                . '?autoplay=1&rel=0';

                                        } elseif ($video->youtube_url) {

                                            if (
                                                Str::contains(
                                                    $video->youtube_url,
                                                    ['youtube.com', 'youtu.be']
                                                )
                                            ) {

                                                preg_match(
                                                    '/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/',
                                                    $video->youtube_url,
                                                    $matches
                                                );

                                                $embedUrl =
                                                    isset($matches[1])
                                                    ? 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1&rel=0'
                                                    : $video->youtube_url;

                                            } elseif (
                                                Str::contains(
                                                    $video->youtube_url,
                                                    'vimeo.com'
                                                )
                                            ) {

                                                preg_match(
                                                    '/vimeo\.com\/(\d+)/',
                                                    $video->youtube_url,
                                                    $matches
                                                );

                                                $embedUrl =
                                                    isset($matches[1])
                                                    ? 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1'
                                                    : $video->youtube_url;
                                            }
                                        }

                                    @endphp


                                    <div
                                        class="swiper-slide"
                                        style="height: 100%;"
                                    >

                                        <div
                                            class="thumb-item"
                                            style="
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                background: #111827;
                                                color: #fff;
                                                position: relative;
                                                border-radius: 6px;
                                                cursor: pointer;
                                                height: 100%;
                                                width: 100%;
                                                overflow: hidden;
                                            "
                                            @click="
                                                activeMediaType = 'video_url';
                                                activeMediaUrl = '{{ $embedUrl }}';

                                                $el
                                                    .closest('.swiper-wrapper')
                                                    .querySelectorAll('.thumb-item')
                                                    .forEach(e => e.classList.remove('active'));

                                                $el.classList.add('active');
                                            "
                                        >

                                            {{-- Miniatura de YouTube --}}
                                            @if($video->video_id)

                                                <img
                                                    src="https://img.youtube.com/vi/{{ $video->video_id }}/hqdefault.jpg"
                                                    alt="Video"
                                                    style="
                                                        width: 100%;
                                                        height: 100%;
                                                        object-fit: cover;
                                                        opacity: 0.7;
                                                        display: block;
                                                    "
                                                >

                                            @endif


                                            {{-- Icono Play --}}
                                            <svg
                                                style="
                                                    width: 24px;
                                                    height: 24px;
                                                    color: #ef4444;
                                                    position: absolute;
                                                    z-index: 2;
                                                    left: 50%;
                                                    top: 50%;
                                                    transform: translate(-50%, -50%);
                                                "
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >

                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                    clip-rule="evenodd"
                                                />

                                            </svg>


                                            {{-- Etiqueta VIDEO --}}
                                            <span
                                                style="
                                                    position: absolute;
                                                    bottom: 0;
                                                    left: 0;
                                                    font-size: 9px;
                                                    font-weight: 700;
                                                    background: rgba(0,0,0,0.7);
                                                    width: 100%;
                                                    text-align: center;
                                                    padding: 2px 0;
                                                    color: #fff;
                                                    z-index: 2;
                                                "
                                            >
                                                VIDEO
                                            </span>

                                        </div>

                                    </div>

                                @endforeach

                            @endif


                            {{-- =================================================
                                 MINIATURAS GALERÍA DE IMÁGENES
                            ================================================== --}}
                            @if($hasImages)

                                @foreach($product->images as $additionalImage)

                                    <div
                                        class="swiper-slide"
                                        style="height: 100%;"
                                    >

                                        <div
                                            class="thumb-item"
                                            style="
                                                height: 100%;
                                                width: 100%;
                                                overflow: hidden;
                                                cursor: pointer;
                                                border-radius: 6px;
                                            "
                                            @click="
                                                activeMediaType = 'image';
                                                activeMediaUrl = '{{ Storage::disk('r2')->url($additionalImage->image) }}';

                                                $el
                                                    .closest('.swiper-wrapper')
                                                    .querySelectorAll('.thumb-item')
                                                    .forEach(e => e.classList.remove('active'));

                                                $el.classList.add('active');
                                            "
                                        >

                                            <img
                                                src="{{ Storage::disk('r2')->url($additionalImage->image) }}"
                                                alt="Adicional"
                                                style="
                                                    width: 100%;
                                                    height: 100%;
                                                    object-fit: cover;
                                                    display: block;
                                                "
                                            >

                                        </div>

                                    </div>

                                @endforeach

                            @endif

                        </div>

                    </div>

                </div>

            @endif

        </div>


        {{-- =========================================================
             PANEL DE INFORMACIÓN
        ========================================================== --}}
        <div class="ap-info-panel">

            <span class="section-eyebrow">
                {{ $product->brand->name ?? 'Genérico' }}
            </span>

            <h1>
                {{ $product->name }}
            </h1>


            <div class="ap-info-meta">
                Categoría:

                @if($product->category)

                    <a href="{{ route('shop.byCategory', $product->category->slug) }}">
                        {{ $product->category->name }}
                    </a>

                @else

                    Sin Categoría

                @endif

            </div>


            {{-- =====================================================
                 BLOQUE DE PRECIOS
            ====================================================== --}}
            <div class="ap-info-price-card">

                @php
                    $showUsd = config('shop.mostrar_precio_usd', true);
                    $showBs = config('shop.mostrar_precio_bs', true);
                @endphp


                @if($product->hasDiscount())

                    <div class="ap-info-price-primary has-discount">

                        @if($showUsd)
                            ${{ number_format($product->price, 2) }}
                        @endif

                        <span
                            style="
                                font-size: 14px;
                                text-decoration: line-through;
                                color: #888;
                                margin-left: 10px;
                            "
                        >
                            ${{ number_format($product->compare_price, 2) }}
                        </span>

                        <span
                            style="
                                font-size: 14px;
                                color: #B12704;
                                font-weight: bold;
                                margin-left: 8px;
                            "
                        >
                            −{{ $product->discount_percentage }}%
                        </span>

                    </div>


                    @if($showBs)

                        <div class="ap-info-price-secondary">

                            Bs. {{ number_format($product->price_bs, 2) }}

                            <span
                                style="
                                    text-decoration: line-through;
                                    margin-left: 6px;
                                    font-size: 12px;
                                "
                            >
                                Bs. {{ number_format($product->compare_price_bs, 2) }}
                            </span>

                        </div>

                    @endif

                @else

                    <div class="ap-info-price-primary">

                        @if($showUsd)
                            ${{ number_format($product->price, 2) }}
                        @endif

                    </div>


                    @if($showBs)

                        <div class="ap-info-price-secondary">
                            Bs. {{ number_format($product->price_bs, 2) }}
                        </div>

                    @endif

                @endif

            </div>


            {{-- =====================================================
                 DESCRIPCIÓN
            ====================================================== --}}
            <div
                class="ap-info-desc"
                style="margin-top: 20px;"
            >

                <h3
                    class="ap-sidebar__section-title"
                    style="margin-bottom: 8px;"
                >
                    Descripción del Producto
                </h3>

                {!! $product->description !!}

            </div>


            {{-- =====================================================
                 DOCUMENTACIÓN PDF
            ====================================================== --}}
            @if($product->pdfs && $product->pdfs->count() > 0)

                <div style="margin-top: 24px;">

                    <h3
                        class="ap-sidebar__section-title"
                        style="margin-bottom: 12px;"
                    >
                        Documentación y Archivos
                    </h3>


                    <div
                        style="
                            display: flex;
                            flex-direction: column;
                            gap: 10px;
                        "
                    >

                        @foreach($product->pdfs as $pdf)

                            <div
                                style="
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    padding: 12px 16px;
                                    background-color: #f9fafb;
                                    border: 1px solid #e5e7eb;
                                    border-radius: 8px;
                                "
                            >

                                <div
                                    style="
                                        display: flex;
                                        align-items: center;
                                        gap: 12px;
                                        overflow: hidden;
                                    "
                                >

                                    <svg
                                        style="
                                            width: 28px;
                                            height: 28px;
                                            color: #ef4444;
                                            flex-shrink: 0;
                                        "
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >

                                        <path
                                            fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                            clip-rule="evenodd"
                                        />

                                    </svg>


                                    <div style="overflow: hidden;">

                                        <strong
                                            style="
                                                font-size: 14px;
                                                display: block;
                                                color: #111827;
                                                white-space: nowrap;
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                            "
                                        >
                                            {{ $pdf->title ?: 'Ficha Técnica PDF' }}
                                        </strong>

                                        <span
                                            style="
                                                font-size: 12px;
                                                color: #6b7280;
                                            "
                                        >
                                            Documento oficial
                                        </span>

                                    </div>

                                </div>


                                <a
                                    href="{{ Storage::disk('r2')->url($pdf->file_path) }}"
                                    target="_blank"
                                    download
                                    style="
                                        background-color: #2563eb;
                                        color: #fff;
                                        padding: 6px 14px;
                                        border-radius: 6px;
                                        text-decoration: none;
                                        font-size: 13px;
                                        font-weight: 500;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 6px;
                                        flex-shrink: 0;
                                        transition: background 0.2s;
                                    "
                                >

                                    <svg
                                        style="
                                            width: 14px;
                                            height: 14px;
                                        "
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                        />

                                    </svg>

                                    Descargar

                                </a>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- =====================================================
                 AGREGAR AL CARRITO
            ====================================================== --}}
            <div
                style="
                    max-width: 300px;
                    margin-top: 25px;
                "
            >
                <x-add-to-cart-button :product="$product" />
            </div>

        </div>

    </div>


    {{-- =============================================================
         RESEÑAS
    ============================================================== --}}
    <div class="ap-reviews-section">

        <div class="ap-reviews-layout">

            {{-- =========================
                 CREAR RESEÑA
            ========================== --}}
            <div class="ap-review-form-card">

                <h3>
                    Escribe una reseña
                </h3>

                @auth

                    <form
                        action="{{ route('products.reviews.store', $product->id) }}"
                        method="POST"
                    >

                        @csrf


                        <div class="ap-form-group">

                            <label for="rating">
                                Calificación
                            </label>

                            <select
                                name="rating"
                                id="rating"
                                class="ap-form-control"
                                required
                            >

                                <option value="5">
                                    ★★★★★ (5/5)
                                </option>

                                <option value="4">
                                    ★★★★☆ (4/5)
                                </option>

                                <option value="3">
                                    ★★★☆☆ (3/5)
                                </option>

                                <option value="2">
                                    ★★☆☆☆ (2/5)
                                </option>

                                <option value="1">
                                    ★☆☆☆☆ (1/5)
                                </option>

                            </select>

                        </div>


                        <div class="ap-form-group">

                            <label for="comment">
                                Tu Comentario
                            </label>

                            <textarea
                                name="comment"
                                id="comment"
                                rows="4"
                                class="ap-form-control"
                                placeholder="¿Qué te pareció la calidad de esta pieza?"
                                required
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="ap-sidebar__btn-submit"
                            style="padding: 11px 0;"
                        >
                            Enviar Opinión
                        </button>

                    </form>

                @else

                    <p
                        style="
                            font-size: 13px;
                            color: var(--text-muted);
                        "
                    >
                        Debes

                        <a
                            href="{{ route('login') }}"
                            style="
                                color: var(--text-link);
                                font-weight: bold;
                            "
                        >
                            iniciar sesión
                        </a>

                        para calificar este producto.
                    </p>

                @endauth

            </div>


            {{-- =========================
                 LISTADO DE RESEÑAS
            ========================== --}}
            <div class="ap-reviews-list">

                <h3>
                    Opiniones de Compradores
                </h3>


                <div
                    style="
                        display: flex;
                        flex-direction: column;
                    "
                >

                    @forelse(
                        $product->reviews()
                            ->where('is_approved', true)
                            ->latest()
                            ->get()
                        as $review
                    )

                        <div class="ap-review-row">

                            <div class="ap-review-row__meta">

                                {{ $review->user->name }}

                                <span class="ap-review-row__date">
                                    {{ $review->created_at->format('d/m/Y') }}
                                </span>

                            </div>


                            <div class="ap-review-row__stars">

                                @for($i = 1; $i <= 5; $i++)

                                    @if($i <= $review->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif

                                @endfor

                            </div>


                            <p class="ap-review-row__text">
                                {{ $review->comment }}
                            </p>

                        </div>

                    @empty

                        <div
                            class="ap-catalog__empty"
                            style="padding: 30px 20px;"
                        >

                            <p class="ap-catalog__empty-text">
                                Este repuesto aún no tiene reseñas.
                                ¡Sé el primero en aportar!
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


    {{-- =============================================================
         SWIPER
    ============================================================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            if (typeof Swiper !== 'undefined') {

                new Swiper(".thumbSwiper", {

                    slidesPerView: 4,

                    spaceBetween: 10,

                    observer: true,

                    observeParents: true,

                    breakpoints: {

                        480: {
                            slidesPerView: 4
                        },

                        1024: {
                            slidesPerView: 5
                        }

                    }

                });

            }

        });
    </script>

</x-front-layout>