<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white bg-blue-600 p-4 rounded-lg shadow">
            {{ __('MANAJEMEN PAYMENT PROVIDER') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-blue-600 p-4">
                <div class="p-6">
                    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                        <x-show-entries :route="route('payment_providers.index')" :search="request()->search" class="w-full md:w-auto"></x-show-entries>
                        <h3 class="text-md md:text-lg font-medium text-blue-600">DAFTAR PAYMENT PROVIDER</h3>
                        <button type="button" onclick="toggleModal('createProviderModal')"
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs md:text-sm px-4 py-2 md:px-5 md:py-2.5 shadow-md">
                            + Tambah Provider
                        </button>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-xs md:text-sm text-left text-gray-900">
                            <thead class="text-xs text-white uppercase bg-blue-600">
                                <tr>
                                    <th scope="col" class="px-4 py-2 md:px-6 md:py-3">#</th>
                                    <th scope="col" class="px-4 py-2 md:px-6 md:py-3">NAMA</th>
                                    <th scope="col" class="px-4 py-2 md:px-6 md:py-3">NOMOR REKENING</th>
                                    <th scope="col" class="px-4 py-2 md:px-6 md:py-3">TIPE</th>
                                    <th scope="col" class="px-4 py-2 md:px-6 md:py-3">STATUS</th>
                                    <th scope="col" class="px-4 py-2 md:px-6 md:py-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = $providers->firstItem();
                                @endphp
                                @foreach ($providers as $provider)
                                    <tr class="bg-white border-b hover:bg-blue-50">
                                        <td class="px-4 py-2 md:px-6 md:py-4 font-semibold">{{ $no++ }}</td>
                                        <td class="px-4 py-2 md:px-6 md:py-4 font-bold text-blue-600">
                                            <div class="flex items-center gap-2">
                                                @if ($provider->logo)
                                                    <img src="{{ asset($provider->logo) }}"
                                                        class="w-8 h-8 rounded-full object-cover" alt="Logo Provider">
                                                @endif
                                                {{ $provider->name }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 md:px-6 md:py-4">{{ $provider->account_number }}</td>
                                        <td class="px-4 py-2 md:px-6 md:py-4">{{ ucfirst($provider->type) }}</td>
                                        <td class="px-4 py-2 md:px-6 md:py-4">
                                            <button onclick="toggleStatus('{{ $provider->id }}')"
                                                class="relative inline-flex items-center w-10 h-6 transition-colors duration-300 ease-in-out rounded-full cursor-pointer
                                                {{ $provider->is_active ? 'bg-green-500' : 'bg-red-500' }}">
                                                <span class="sr-only">Toggle Status</span>
                                                <span class="absolute left-0 w-6 h-6 transition-transform duration-300 ease-in-out transform bg-white rounded-full shadow-md
                                                    {{ $provider->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                            </button>
                                        </td>
                                        <td class="px-4 py-2 md:px-6 md:py-4 space-x-2 flex flex-wrap">
                                            <button data-id="{{ $provider->id }}" data-name="{{ $provider->name }}"
                                                data-account_number="{{ $provider->account_number }}"
                                                data-account_name="{{ $provider->account_name }}"
                                                data-type="{{ $provider->type }}"
                                                data-instructions="{{ $provider->instructions }}"
                                                data-logo="{{ $provider->logo }}"
                                                onclick="editProviderModal(this)"
                                                class="edit-button">
                                                ✏️
                                            </button>
                                            <form action="{{ route('payment_providers.destroy', $provider->id) }}" method="POST"
                                                class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-button">
                                                    🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-blue-600 text-xs md:text-sm">
                        {{ $providers->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Provider -->
    <div id="createProviderModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded-lg shadow-2xl mx-4 md:mx-auto md:w-1/2 border-2 border-blue-600">
            <div class="flex items-start justify-between p-6 border-b-2 border-blue-600">
                <h3 class="text-xl font-semibold text-blue-600">🆕 Tambah Provider Baru</h3>
                <button type="button" onclick="toggleModal('createProviderModal')"
                    class="text-blue-600 hover:text-blue-800 text-2xl">
                    ×
                </button>
            </div>
            <form id="createProviderForm" action="{{ route('payment_providers.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="name_create" class="block mb-2 text-sm font-medium text-blue-600">Nama Provider</label>
                        <input type="text" name="name" id="name_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required value="{{ old('name') }}" placeholder="Contoh: Dana">
                    </div>
                    <div class="mb-4">
                        <label for="account_number_create" class="block mb-2 text-sm font-medium text-blue-600">Nomor Rekening</label>
                        <input type="text" name="account_number" id="account_number_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required value="{{ old('account_number') }}" placeholder="Contoh: 081234567890">
                    </div>
                    <div class="mb-4">
                        <label for="account_name_create" class="block mb-2 text-sm font-medium text-blue-600">Nama Akun</label>
                        <input type="text" name="account_name" id="account_name_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required value="{{ old('account_name') }}" placeholder="Contoh: Ajnira Ramen">
                    </div>
                    <div class="mb-4">
                        <label for="type_create" class="block mb-2 text-sm font-medium text-blue-600">Tipe</label>
                        <select name="type" id="type_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="e-wallet" {{ old('type') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-4 md:col-span-2">
                        <label for="instructions_create" class="block mb-2 text-sm font-medium text-blue-600">Instruksi Pembayaran</label>
                        <textarea name="instructions" id="instructions_create" rows="3"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="Tambahkan instruksi pembayaran...">{{ old('instructions') }}</textarea>
                    </div>
                    <div class="mb-4 md:col-span-2">
                        <label for="logo_create" class="block mb-2 text-sm font-medium text-blue-600">Logo Provider</label>
                        <div class="flex items-center gap-4">
                            <div id="logoPreviewCreate" class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden hidden">
                                <img class="w-full h-full object-cover" alt="Preview Logo">
                            </div>
                            <input type="file" name="logo" id="logo_create"
                                class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                accept="image/*" onchange="showLogoPreview(event, 'create')">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-4">
                    <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 shadow-md">
                        💾 Simpan
                    </button>
                    <button type="button" onclick="toggleModal('createProviderModal')"
                        class="text-blue-600 bg-white hover:bg-blue-50 border-2 border-blue-600 rounded-lg text-sm font-medium px-5 py-2.5">
                        ✖ Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Provider -->
    <div id="editProviderModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded-lg shadow-2xl mx-4 md:mx-auto md:w-1/2 border-2 border-blue-600">
            <div class="flex items-start justify-between p-6 border-b-2 border-blue-600">
                <h3 class="text-xl font-semibold text-blue-600" id="title_edit">✏️ Update Provider</h3>
                <button type="button" onclick="toggleModal('editProviderModal')"
                    class="text-blue-600 hover:text-blue-800 text-2xl">
                    ×
                </button>
            </div>
            <form id="editProviderForm" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="name_edit" class="block mb-2 text-sm font-medium text-blue-600">Nama Provider</label>
                        <input type="text" name="name" id="name_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                    </div>
                    <div class="mb-4">
                        <label for="account_number_edit" class="block mb-2 text-sm font-medium text-blue-600">Nomor Rekening</label>
                        <input type="text" name="account_number" id="account_number_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                    </div>
                    <div class="mb-4">
                        <label for="account_name_edit" class="block mb-2 text-sm font-medium text-blue-600">Nama Akun</label>
                        <input type="text" name="account_name" id="account_name_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                    </div>
                    <div class="mb-4">
                        <label for="type_edit" class="block mb-2 text-sm font-medium text-blue-600">Tipe</label>
                        <select name="type" id="type_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="e-wallet">E-Wallet</option>
                            <option value="bank">Bank</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-4 md:col-span-2">
                        <label for="instructions_edit" class="block mb-2 text-sm font-medium text-blue-600">Instruksi Pembayaran</label>
                        <textarea name="instructions" id="instructions_edit" rows="3"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                    </div>
                    <div class="mb-4 md:col-span-2">
                        <label for="logo_edit" class="block mb-2 text-sm font-medium text-blue-600">Logo Provider</label>
                        <div class="flex items-center gap-4">
                            <div id="currentLogo" class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden">
                                <img class="w-full h-full object-cover" alt="Logo Saat Ini">
                            </div>
                            <div id="logoPreviewEdit" class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden hidden">
                                <img class="w-full h-full object-cover" alt="Preview Logo Baru">
                            </div>
                            <input type="file" name="logo" id="logo_edit"
                                class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                accept="image/*" onchange="showLogoPreview(event, 'edit')">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-4">
                    <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 shadow-md">
                        💾 Simpan
                    </button>
                    <button type="button" onclick="toggleModal('editProviderModal')"
                        class="text-blue-600 bg-white hover:bg-blue-50 border-2 border-blue-600 rounded-lg text-sm font-medium px-5 py-2.5">
                        ✖ Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Tombol Edit */
        .edit-button {
            background: transparent;
            border: 2px solid #FFC107;
            color: #FFC107;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.75rem;
            box-shadow: none;
            transition: border-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .edit-button:hover {
            border-color: #D4A017;
            background: transparent;
            color: #D4A017;
        }
        /* Tombol Hapus */
        .delete-button {
            background: transparent;
            border: 2px solid #DC2626;
            color: #DC2626;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.75rem;
            box-shadow: none;
            transition: border-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .delete-button:hover {
            border-color: #B91C1C;
            background: transparent;
            color: #B91C1C;
        }
        @media (min-width: 768px) {
            .edit-button, .delete-button {
                padding: 8px 16px;
                font-size: 0.875rem;
            }
        }
    </style>

    <script>
        // Fungsi Toggle Modal
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');

            if (modalId === 'createProviderModal') {
                document.getElementById('logoPreviewCreate').classList.add('hidden');
                document.getElementById('logo_create').value = '';
            } else if (modalId === 'editProviderModal') {
                document.getElementById('logoPreviewEdit').classList.add('hidden');
                document.getElementById('logo_edit').value = '';
            }
        }

        // Fungsi Preview Logo
        function showLogoPreview(event, type) {
            const input = event.target;
            const previewId = type === 'create' ? 'logoPreviewCreate' : 'logoPreviewEdit';
            const preview = document.getElementById(previewId);
            const image = preview.querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    image.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Fungsi Edit Modal
        function editProviderModal(button) {
            const id = button.dataset.id;
            const form = document.getElementById('editProviderForm');
            const currentLogo = document.getElementById('currentLogo').querySelector('img');

            form.action = `/payment_providers/${id}`;
            document.getElementById('name_edit').value = button.dataset.name;
            document.getElementById('account_number_edit').value = button.dataset.account_number;
            document.getElementById('account_name_edit').value = button.dataset.account_name;
            document.getElementById('type_edit').value = button.dataset.type;
            document.getElementById('instructions_edit').value = button.dataset.instructions || '';

            if (button.dataset.logo) {
                currentLogo.src = "{{ asset('') }}" + button.dataset.logo;
            } else {
                currentLogo.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23CBD5E0"><path d="M4 5h16v12H4z"/><path d="M12 9a3 3 0 100 6 3 3 0 000-6z"/></svg>';
            }

            document.getElementById('title_edit').innerText = `✏️ Update ${button.dataset.name}`;
            toggleModal('editProviderModal');
        }

        // Fungsi Toggle Status
        async function toggleStatus(id) {
            const response = await fetch(`/payment_providers/${id}/toggle-status`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Status berhasil diubah.',
                    confirmButtonColor: '#2563eb',
                    timer: 2000
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal mengubah status.',
                    confirmButtonColor: '#2563eb'
                });
            }
        }

        // Konfirmasi Hapus
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = this.closest('tr').querySelector('td:nth-child(2) .flex').textContent.trim();

                Swal.fire({
                    title: 'Hapus Provider?',
                    html: `Yakin ingin menghapus <b>"${name}"</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) this.submit();
                });
            });
        });

        // Notifikasi
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563eb',
                timer: 3000
            });
        @endif
        @if (session('error_message'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error_message') }}',
                confirmButtonColor: '#2563eb',
                timer: 5000
            });
        @endif
    </script>
</x-app-layout>