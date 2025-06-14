<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white bg-blue-600 p-4 rounded-lg shadow">
            {{ __('MANAJEMEN TRANSAKSI') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-blue-600">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <x-show-entries :route="route('transaction.index')" :search="request()->search" class="w-full md:w-auto" />
                        <h3 class="text-lg font-medium text-blue-600">DAFTAR TRANSAKSI</h3>
                        <button onclick="toggleModal('createTransactionModal')"
                            class="text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg shadow-md">
                            + Transaksi Baru
                        </button>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-900">
                            <thead class="text-xs text-white uppercase bg-blue-600">
                                <tr>
                                    <th class="px-6 py-3">#</th>
                                    <th class="px-6 py-3">User</th>
                                    <th class="px-6 py-3">Meja</th>
                                    <th class="px-6 py-3">Ukuran</th>
                                    <th class="px-6 py-3">Level Pedas</th>
                                    <th class="px-6 py-3">Total</th>
                                    <th class="px-6 py-3">Metode Bayar</th>
                                    <th class="px-6 py-3">Bukti Bayar</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr class="bg-white border-b hover:bg-blue-50">
                                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 font-bold text-blue-600">
                                            {{ optional($transaction->user)->name ?? 'User Tidak Diketahui' }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-blue-600">
                                            {{ $transaction->table->number ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 capitalize">{{ $transaction->bowl_size }}</td>
                                        <td class="px-6 py-4 capitalize">{{ $transaction->spiciness_level }}</td>
                                        <td class="px-6 py-4">
                                            Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            @if ($transaction->paymentProvider)
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ asset($transaction->paymentProvider->logo) }}"
                                                        class="w-8 h-8 object-contain rounded-lg">
                                                    <span>{{ $transaction->paymentProvider->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-red-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($transaction->payment_proof)
                                                <button onclick="showImageModal('{{ asset($transaction->payment_proof) }}')"
                                                    class="inline-block group relative">
                                                    <img src="{{ asset($transaction->payment_proof) }}"
                                                        class="w-16 h-16 object-cover rounded-lg border-2 border-blue-200 transition-transform group-hover:scale-110"
                                                        alt="Bukti pembayaran transaksi {{ $transaction->id }}">
                                                    <span
                                                        class="absolute bottom-1 right-1 bg-black/50 text-white text-xs px-2 py-1 rounded-full">🔍
                                                        Lihat</span>
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'paid' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                    'proses' => 'bg-blue-100 text-blue-800',
                                                ];
                                            @endphp
                                            <span
                                                class="px-3 py-1.5 text-sm font-semibold rounded-full {{ $statusColors[$transaction->status] }}">
                                                {{ strtoupper($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 space-x-2 flex items-center">
                                            <button onclick="processTransaction('{{ $transaction->id }}')"
                                                class="process-button">
                                                🔄 Proses
                                            </button>
                                            <button onclick="editTransactionModal(this)"
                                                data-id="{{ $transaction->id }}"
                                                data-user_id="{{ $transaction->user_id }}"
                                                data-table_id="{{ $transaction->table_id }}"
                                                data-total_price="{{ $transaction->total_price }}"
                                                data-status="{{ $transaction->status }}"
                                                data-payment_provider_id="{{ $transaction->payment_provider_id }}"
                                                data-payment_proof="{{ $transaction->payment_proof }}"
                                                data-bowl_size="{{ $transaction->bowl_size }}"
                                                data-spiciness_level="{{ $transaction->spiciness_level }}"
                                                class="edit-button">
                                                ✏️ Edit
                                            </button>
                                            <button onclick="confirmDelete('{{ $transaction->id }}')"
                                                class="delete-button">
                                                🗑️ Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-blue-600">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Transaksi -->
    <div id="createTransactionModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded-lg shadow-2xl mx-4 md:mx-auto md:w-1/2 border-2 border-blue-600">
            <div class="flex items-start justify-between p-6 border-b-2 border-blue-600">
                <h3 class="text-xl font-semibold text-blue-600">➕ Tambah Transaksi Baru</h3>
                <button onclick="toggleModal('createTransactionModal')"
                    class="text-blue-600 hover:text-blue-800 text-2xl">×</button>
            </div>
            <form id="createTransactionForm" onsubmit="event.preventDefault(); submitTransactionForm('createTransactionForm')" action="{{ route('transaction.store') }}" method="POST" class="p-6"
                enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="user_id_create" class="block mb-2 text-sm font-medium text-blue-600">Pilih
                            User</label>
                        <select name="user_id" id="user_id_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="">Pilih User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="table_id_create" class="block mb-2 text-sm font-medium text-blue-600">Pilih
                            Meja</label>
                        <select name="table_id" id="table_id_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="">Pilih Meja</option>
                            @foreach ($availableTables as $table)
                                <option value="{{ $table->id }}">{{ $table->number }} -
                                    {{ strtoupper($table->status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="total_price_create" class="block mb-2 text-sm font-medium text-blue-600">Total
                            Harga</label>
                        <input type="number" name="total_price" id="total_price_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="0" step="100" required>
                    </div>
                    <div>
                        <label for="status_create" class="block mb-2 text-sm font-medium text-blue-600">Status</label>
                        <select name="status" id="status_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="pending">Pending</option>
                            <option value="proses">Proses</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_provider_id_create"
                            class="block mb-2 text-sm font-medium text-blue-600">Metode Pembayaran</label>
                        <select name="payment_provider_id" id="payment_provider_id_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="">Pilih Metode</option>
                            @foreach ($paymentProviders as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="bowl_size_create" class="block mb-2 text-sm font-medium text-blue-600">Ukuran
                            Mangkok</label>
                        <select name="bowl_size" id="bowl_size_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="small">Kecil</option>
                            <option value="medium" selected>Sedang</option>
                            <option value="large">Besar</option>
                        </select>
                    </div>
                    <div>
                        <label for="spiciness_level_create" class="block mb-2 text-sm font-medium text-blue-600">Level
                            Pedas</label>
                        <select name="spiciness_level" id="spiciness_level_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="mild">Level 1</option>
                            <option value="medium">Level 2</option>
                            <option value="hot">Level 3</option>
                            <option value="extreme">Level 4</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_proof_create" class="block mb-2 text-sm font-medium text-blue-600">Bukti
                            Transfer</label>
                        <input type="file" name="payment_proof" id="payment_proof_create"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            accept="image/*" required>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 shadow-md">
                        💾 Simpan
                    </button>
                    <button type="button" onclick="toggleModal('createTransactionModal')"
                        class="text-blue-600 bg-white hover:bg-blue-50 border-2 border-blue-600 rounded-lg text-sm font-medium px-5 py-2.5">
                        ✖️ Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Transaksi -->
    <div id="editTransactionModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded-lg shadow-2xl mx-4 md:mx-auto md:w-1/2 border-2 border-blue-600">
            <div class="flex items-start justify-between p-6 border-b-2 border-blue-600">
                <h3 class="text-xl font-semibold text-blue-600">✏️ Edit Transaksi</h3>
                <button onclick="toggleModal('editTransactionModal')"
                    class="text-blue-600 hover:text-blue-800 text-2xl">×</button>
            </div>
            <form id="editTransactionForm" onsubmit="event.preventDefault(); submitTransactionForm('editTransactionForm')" method="POST" class="p-6" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 space-y-4 max-h-[70vh] modal-scrollable overflow-y-auto">
                    <div>
                        <label for="user_id_edit" class="block mb-2 text-sm font-medium text-blue-600">Pilih
                            User</label>
                        <select name="user_id" id="user_id_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="">Pilih User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="table_id_edit" class="block mb-2 text-sm font-medium text-blue-600">Pilih
                            Meja</label>
                        <select name="table_id" id="table_id_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="">Pilih Meja</option>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->number }} -
                                    {{ strtoupper($table->status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="total_price_edit" class="block mb-2 text-sm font-medium text-blue-600">Total
                            Harga</label>
                        <input type="number" name="total_price" id="total_price_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="0" step="100" required>
                    </div>
                    <div>
                        <label for="status_edit" class="block mb-2 text-sm font-medium text-blue-600">Status</label>
                        <select name="status" id="status_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="pending">Pending</option>
                            <option value="proses">Proses</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_provider_id_edit"
                            class="block mb-2 text-sm font-medium text-blue-600">Metode Pembayaran</label>
                        <select name="payment_provider_id" id="payment_provider_id_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="">Pilih Metode</option>
                            @foreach ($paymentProviders as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="bowl_size_edit" class="block mb-2 text-sm font-medium text-blue-600">Ukuran
                            Mangkok</label>
                        <select name="bowl_size" id="bowl_size_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="small">Kecil</option>
                            <option value="medium">Sedang</option>
                            <option value="large">Besar</option>
                        </select>
                    </div>
                    <div>
                        <label for="spiciness_level_edit" class="block mb-2 text-sm font-medium text-blue-600">Level
                            Pedas</label>
                        <select name="spiciness_level" id="spiciness_level_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                            <option value="mild">Level 1</option>
                            <option value="medium">Level 2</option>
                            <option value="hot">Level 3</option>
                            <option value="extreme">Level 4</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_proof_edit" class="block mb-2 text-sm font-medium text-blue-600">Bukti
                            Transfer</label>
                        <input type="file" name="payment_proof" id="payment_proof_edit"
                            class="bg-white border-2 border-blue-600 text-blue-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            accept="image/*">
                        <div id="current_proof" class="mt-2 hidden">
                            <p class="text-xs text-gray-500">Bukti Saat Ini:</p>
                            <img src="" class="w-20 h-20 object-cover rounded-lg border mt-1">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 shadow-md">
                        💾 Update
                    </button>
                    <button type="button" onclick="toggleModal('editTransactionModal')"
                        class="text-blue-600 bg-white hover:bg-blue-50 border-2 border-blue-600 rounded-lg text-sm font-medium px-5 py-2.5">
                        ✖️ Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-90 flex items-center justify-center"
         onclick="closeImageModalIfBackground(event)">
        <div class="max-w-4xl max-h-[90vh]">
            <img id="modalImage" class="max-h-[80vh] rounded-lg" alt="Bukti pembayaran">
            <button onclick="closeImageModal()"
                    class="absolute top-4 right-4 text-white text-3xl hover:text-gray-200">
                ×
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.13.5/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Tombol Proses */
        .process-button {
            background: transparent;
            border: 2px solid #3B82F6;
            color: #3B82F6;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.75rem;
            box-shadow: none;
            transition: border-color 0.3s ease, color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .process-button:hover {
            border-color: #2563EB;
            color: #2563EB;
            background: transparent;
        }

        /* Tombol Edit */
        .edit-button {
            background: transparent;
            border: 2px solid #FFC107;
            color: #FFC107;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.75rem;
            box-shadow: none;
            transition: border-color 0.3s ease, color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .edit-button:hover {
            border-color: #D4A017;
            color: #D4A017;
            background: transparent;
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
            transition: border-color 0.3s ease, color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .delete-button:hover {
            border-color: #B91C1C;
            color: #B91C1C;
            background: transparent;
        }

        @media (min-width: 768px) {
            .process-button,
            .edit-button,
            .delete-button {
                padding: 8px 16px;
                font-size: 0.875rem;
            }
        }

        /* Styling tambahan untuk modal dan elemen lainnya */
        .modal-scrollable {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .modal-scrollable::-webkit-scrollbar {
            display: none;
        }

        .modal-transition {
            transition: opacity 0.3s ease;
        }

        .proof-transition {
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .modal-overlay {
            z-index: 9998;
        }

        .modal-content {
            z-index: 9999;
        }

        .capitalize {
            text-transform: capitalize;
        }

        #imageModal {
            backdrop-filter: blur(5px);
        }

        #imageModal img {
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
        }

        @media (min-width: 768px) {
            #modalImage {
                max-height: 80vh;
            }
        }

        #modalImage:hover {
            transform: scale(1.02);
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.plugin(AlpineCollapse);

            Alpine.data('transaction', () => ({
                open: false,
                toggle() {
                    this.open = !this.open
                }
            }));
        });

        window.toggleModal = function(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        window.showImageModal = function(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        window.closeImageModal = function() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        window.closeImageModalIfBackground = function(event) {
            if (event.target.id === 'imageModal') {
                closeImageModal();
            }
        };

        window.editTransactionModal = function(button) {
            const transactionData = {
                id: button.dataset.id,
                user_id: button.dataset.user_id,
                table_id: button.dataset.table_id,
                total_price: button.dataset.total_price,
                status: button.dataset.status,
                payment_provider_id: button.dataset.payment_provider_id,
                payment_proof: button.dataset.payment_proof,
                bowl_size: button.dataset.bowl_size,
                spiciness_level: button.dataset.spiciness_level
            };

            const form = document.getElementById('editTransactionForm');
            form.action = `/transaction/${transactionData.id}`;

            document.getElementById('user_id_edit').value = transactionData.user_id;
            document.getElementById('table_id_edit').value = transactionData.table_id;
            document.getElementById('total_price_edit').value = transactionData.total_price;
            document.getElementById('status_edit').value = transactionData.status;
            document.getElementById('payment_provider_id_edit').value = transactionData.payment_provider_id;
            document.getElementById('bowl_size_edit').value = transactionData.bowl_size;
            document.getElementById('spiciness_level_edit').value = transactionData.spiciness_level;

            const currentProof = document.querySelector('#current_proof img');
            if (transactionData.payment_proof) {
                currentProof.src = "{{ asset('storage') }}/" + transactionData.payment_proof;
                document.getElementById('current_proof').classList.remove('hidden');
            } else {
                document.getElementById('current_proof').classList.add('hidden');
            }

            toggleModal('editTransactionModal');
        };

        window.processTransaction = async function(id) {
            console.log('processTransaction called with id:', id);
            try {
                const konfirmasi = await Swal.fire({
                    title: 'Proses Transaksi?',
                    text: "Status transaksi akan diubah menjadi PROSES",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Proses!',
                    cancelButtonText: 'Batal'
                });

                if (!konfirmasi.isConfirmed) return;

                const response = await fetch(`/transaction/${id}/status`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'proses'
                    })
                });

                const result = await response.json();
                console.log('Response from server:', result);

                if (!response.ok) {
                    throw new Error(result.message || 'Gagal memperbarui status');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Status transaksi telah diubah menjadi PROSES',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });

            } catch (error) {
                console.error('Error in processTransaction:', error);
                Swal.fire('Error!', error.message, 'error');
            }
        };

        window.confirmDelete = async function(id) {
            console.log('confirmDelete called with id:', id);
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus Transaksi?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            });

            if (isConfirmed) {
                try {
                    const response = await fetch(`/transaction/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (!response.ok) throw new Error('Gagal menghapus transaksi');
                    console.log('Transaction deleted successfully');
                    window.location.reload();
                } catch (error) {
                    console.error('Error in confirmDelete:', error);
                    Swal.fire('Error!', error.message, 'error');
                }
            }
        };

        window.submitTransactionForm = async function(formId) {
            console.log('submitTransactionForm called for form:', formId);
            const form = document.getElementById(formId);
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: form.method === 'POST' ? 'POST' : 'PATCH',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                console.log('Response from server:', result);

                if (!response.ok) {
                    throw new Error(result.message || 'Terjadi kesalahan server');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message || 'Transaksi berhasil disimpan',
                    confirmButtonColor: '#3085d6',
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } catch (error) {
                console.error('Error in submitTransactionForm:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyimpan!',
                    text: error.message,
                    confirmButtonColor: '#d33'
                });
            }
        };

        // Notifikasi berdasarkan sesi
        @if (session('success'))
            console.log('Sesi success tersedia: {{ session('success') }}');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                timer: 3000
            });
        @endif
        @if (session('message_insert'))
            console.log('Sesi message_insert tersedia: {{ session('message_insert') }}');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('message_insert') }}',
                confirmButtonColor: '#3085d6',
                timer: 3000
            });
        @endif
        @if (session('error_message'))
            console.log('Sesi error_message tersedia: {{ session('error_message') }}');
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error_message') }}',
                confirmButtonColor: '#d33',
                timer: 5000
            });
        @endif
    </script>
</x-app-layout>