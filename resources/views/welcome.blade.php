<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Seblak Ajnira - Parasmanan Terenak!</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@splidejs/splide-extension-auto-scroll@0.5.3/dist/js/splide-extension-auto-scroll.min.js">
    </script>
    <style>
        .splide__slide {
            max-width: 140px !important;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        @media (min-width: 768px) {
            .splide__slide {
                max-width: 280px !important;
            }
        }

        .splide__slide.is-active {
            transform: scale(1.05);
            opacity: 1;
        }

        .splide__slide:not(.is-active) {
            opacity: 0.8;
        }

        .splide__arrow {
            background: #dc2626 !important;
            border-radius: 50%;
            padding: 4px;
            transition: background 0.3s ease;
        }

        @media (min-width: 768px) {
            .splide__arrow {
                padding: 8px;
            }
        }

        .splide__arrow:hover {
            background: #b91c1c !important;
        }

        .splide__pagination__page.is-active {
            background: #dc2626 !important;
            transform: scale(1.2);
        }

        .splide__pagination__page {
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .menu-card {
            max-width: 140px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .menu-card {
                max-width: 280px;
            }
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        @media (min-width: 768px) {
            .menu-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            }
        }

        .btn-order {
            transition: transform 0.2s ease, background 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .btn-order:hover {
            transform: scale(1.05);
            background: #b91c1c;
        }

        .price-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 50%;
        }

        /* Penyesuaian untuk mobile */
        #map {
            block-size: 300px; /* Default untuk mobile */
            inline-size: 100%;
        }

        @media (min-width: 768px) {
            #map {
                block-size: 695px; /* Tinggi untuk layar besar */
            }
        }

        @media (max-width: 767px) {
            .text-sm {
                font-size: 0.875rem; /* 14px */
            }
            .text-base {
                font-size: 1rem; /* 16px */
            }
        }
    </style>
</head>

<body class="bg-red-50">
    <!-- Header Navigation -->
    <nav class="bg-red-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <div class="flex items-center">
                    <img src="images/logo.png" alt="Logo Seblak Ajnira" class="w-8 h-8 mr-2 rounded-full">
                    <h1 class="text-xl font-bold">Seblak Ajnira</h1>
                </div>
            </div>
            @auth
                <a href="{{ url('/user_interface') }}"
                    class="bg-red-500 hover:bg-red-700 px-4 py-2 rounded-lg transition-colors">
                    Dashboard
                </a>
            @else
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="hover:text-red-200 transition-colors">Login</a>
                    <a href="{{ route('register') }}"
                        class="bg-red-500 hover:bg-red-700 px-4 py-2 rounded-lg transition-colors">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative h-96 bg-red-800 text-white overflow-hidden flex items-center justify-center bg-cover bg-center"
        style="background-image: url('/images/background-seblak.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative text-center px-4 z-10">
            <h2 class="text-4xl font-bold mb-4">
                Pedasnya Bikin Cerita,<br>
                <span>Nikmatnya Bikin Balik Lagi !</span>
            </h2>
        </div>
    </div>

    <!-- Menu Carousel -->
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-8 text-red-600">Menu Andalan Kami</h3>
            <div id="seblak-carousel" class="splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/seblak-kuah.jpg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Seblak Kuah">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Seblak Kuah</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp10.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/seblak-kering.jpg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Seblak Kering">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Seblak Kering</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp10.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/seblak-nyemek.jpg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Seblak Nyemek">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Seblak Nyemek</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp10.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/seblak-cobek.jpg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Seblak Cobek">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Seblak Cobek</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp10.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/seblak-special.jpg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Seblak Special">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Seblak Special</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp15.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Minuman Carousel -->
    <section class="py-10 bg-red-50">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-8 text-red-600">Minuman Segar</h3>
            <div id="minuman-carousel" class="splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/es-teh-manis.jpeg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Es Teh Manis">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Es Teh Manis</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp5.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/teh-manis-hangat.jpeg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Teh Manis Hangat">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Teh Manis Hangat</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp5.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/es-jeruk-peras.jpeg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Es Jeruk Peras">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Es Jeruk Peras</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp5.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/es-milo.jpeg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Es Milo">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Es Milo</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp5.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/es-susu-regal.jpeg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Es Susu Regal">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Es Susu Regal</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp5.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/es-cincau-susu.jpeg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Es Cincau Susu">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Es Cincau Susu</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp5.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide px-2">
                            <div class="menu-card bg-white rounded-lg shadow-lg overflow-hidden mx-auto">
                                <img src="/images/es-kelapa.jpeg" class="w-full h-20 md:h-40 object-cover"
                                    alt="Es Kelapa Gula Aren">
                                <div class="p-3 md:p-5">
                                    <h4 class="font-bold text-sm md:text-lg mb-2">Es Kelapa Gula Aren</h4>
                                    <div class="mt-2 md:mt-4 flex justify-between items-center">
                                        <span
                                            class="price-text text-sm md:text-2xl font-bold text-red-600">Rp5.000</span>
                                        <button
                                            class="btn-order bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-lg hover:bg-red-600 transition text-xs md:text-sm">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="py-10 bg-red-50">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-8 text-red-600">Kenapa Pilih Kami?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-white rounded-lg shadow">
                    <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <div class="mt-6 text-left">
                        <p class="font-semibold mb-2 text-base">✅ Rasa Asli Bandung</p>
                        <p class="mb-4 text-sm">Kami hadirkan rasa seblak khas Bandung yang autentik dan bikin nagih!</p>
                        <p class="font-semibold mb-2 text-base">✅ Bahan Segar Setiap Hari</p>
                        <p class="mb-4 text-sm">Gunakan bahan berkualitas, selalu fresh setiap hari tanpa pengawet.</p>
                        <p class="font-semibold mb-2 text-base">✅ Pelayanan Cepat & Ramah</p>
                        <p class="mb-4 text-sm">Pesanan kamu kami layani dengan cepat, hangat, dan profesional.</p>
                        <p class="font-semibold mb-2 text-base">✅ Banyak Pilihan Level Pedas</p>
                        <p class="mb-4 text-sm">Dari pedas manja sampai pedas gila, kamu bisa pilih sesuai selera.</p>
                        <p class="font-semibold mb-2 text-base">✅ Harga Terjangkau</p>
                        <p class="mb-4 text-sm">Rasa juara, tapi harga tetap bersahabat untuk semua kalangan.</p>
                        <p class="font-semibold mb-2 text-base">✅ Cocok Buat Nongkrong</p>
                        <p class="text-sm">Tempat nyaman buat makan bareng temen atau keluarga.</p>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
                    <div id="map" class="rounded-lg shadow mt-8 md:mt-0"></div>
                    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                    <script>
                        var map = L.map('map').setView([-7.30716, 108.24469], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                        }).addTo(map);
                        L.marker([-7.30716, 108.24469]).addTo(map)
                            .bindPopup('Seblak Ajnira')
                            .openPopup();
                    </script>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-red-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h5 class="text-xl font-bold mb-4">Seblak Ajnira</h5>
                    <p class="text-base">Dusun Pengkolan, Sindangkasih, Kec. Sindangkasih, Kabupaten Ciamis, Jawa Barat 46268</p>
                </div>
                <div>
                    <h5 class="text-xl font-bold mb-4">Kontak Kami</h5>
                    <p class="text-base">
                        📞 <a href="http://whatsapp/6289603750891" target="_blank"
                            class="underline hover:text-gray-300">
                            0896-0375-0891
                        </a>
                    </p>
                    <p class="mt-2 flex items-center gap-x-2 text-base">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png"
                            alt="Ikon Instagram" width="20" height="20">
                        <a href="http://instagram/seblak_ajnira" target="_blank"
                            class="underline hover:text-gray-300">
                            @seblak_ajnira
                        </a>
                    </p>
                </div>
                <div>
                    <h5 class="text-xl font-bold mb-4">Jam Buka</h5>
                    <p class="text-base">Senin - Jumat: 10.00 - 21.00</p>
                    <p class="text-base">Sabtu - Minggu: 11.00 - 22.00</p>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-red-700 text-center">
                <p class="text-base">
                    <a href="http://seblakajnira.com" target="_blank" class="hover:text-gray-300">
                        © 2025 Seblak Ajnira
                    </a>
                </p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seblak Carousel
            new Splide('#seblak-carousel', {
                type: 'loop',
                perPage: 2,
                gap: '1rem', // Diperbarui untuk mobile
                focus: 'center',
                pagination: false,
                arrows: true,
                autoScroll: {
                    speed: 1,
                    pauseOnHover: true,
                },
                direction: 'ltr',
                drag: 'free',
                breakpoints: {
                    640: {
                        perPage: 1
                    },
                    768: {
                        perPage: 3,
                        gap: '2rem', // Diperbarui untuk layar besar
                    },
                }
            }).mount(window.splide.Extensions);

            // Minuman Carousel
            new Splide('#minuman-carousel', {
                type: 'loop',
                perPage: 2,
                gap: '1rem', // Diperbarui untuk mobile
                focus: 'center',
                pagination: false,
                arrows: true,
                autoScroll: {
                    speed: 1, // Diubah untuk pergerakan ke kanan
                    pauseOnHover: true,
                },
                direction: 'ltr', // Diubah ke ltr untuk pergerakan ke kanan
                drag: 'free',
                breakpoints: {
                    640: {
                        perPage: 1
                    },
                    768: {
                        perPage: 3,
                        gap: '2rem', // Diperbarui untuk layar besar
                    },
                }
            }).mount(window.splide.Extensions);
        });
    </script>
</body>

</html>