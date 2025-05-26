<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white bg-red-600 p-4 rounded-lg shadow">
            {{ __('MANAJEMEN MEJA') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-600">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <x-show-entries :route="route('table.index')" :search="request()->search" class="w-full md:w-auto" />
                        <h3 class="text-lg font-medium text-red-600">DATA MEJA RESTORAN</h3>
                        <button type="button" onclick="createTable()"
                            class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 shadow-md">
                            + Tambah Meja
                        </button>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-900">
                            <thead class="text-xs text-white uppercase bg-red-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3">NO</th>
                                    <th scope="col" class="px-6 py-3">NOMOR MEJA</th>
                                    <th scope="col" class="px-6 py-3">STATUS</th>
                                    <th scope="col" class="px-6 py-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @foreach ($tables as $table)
                                    <tr id="row-{{ $table->id }}" class="bg-white border-b hover:bg-red-50"
                                        data-occupied-at="{{ $table->status === 'occupied' && $table->occupied_at ? $table->occupied_at->toIso8601String() : '' }}">
                                        <td class="px-6 py-4 font-semibold">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 font-bold text-red-600">{{ $table->number }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1.5 text-sm font-semibold rounded-full 
                                                {{ $table->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}"
                                                id="status-{{ $table->id }}">
                                                {{ strtoupper($table->status) }}
                                            </span>
                                            @if ($table->status === 'occupied' && $table->occupied_at)
                                                <span id="timer-{{ $table->id }}" class="ml-2 text-xs text-gray-600" aria-live="polite"></span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 space-x-2">
                                            <button
                                                onclick="openEditModal('{{ $table->id }}', '{{ $table->number }}', '{{ $table->status }}')"
                                                class="bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded-md text-sm text-white shadow">
                                                ✏️ Edit
                                            </button>
                                            <button onclick="setAvailable('{{ $table->id }}')"
                                                class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-md text-sm text-white shadow">
                                                ✅ Available
                                            </button>
                                            <button onclick="deleteTable('{{ $table->id }}')"
                                                class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md text-sm text-white shadow">
                                                🗑️ Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-red-600">
                        {{ $tables->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-xl font-bold text-red-600 mb-4">Edit Meja</h3>
            <form id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editId" name="id">
                <div class="mb-4">
                    <label class="block text-red-600 mb-2">Nomor Meja</label>
                    <input type="text" id="editNumber" name="number"
                        class="w-full p-2 border-2 border-red-600 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-red-600 mb-2">Status</label>
                    <select id="editStatus" name="status" class="w-full p-2 border-2 border-red-600 rounded">
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                    </select>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi untuk memformat waktu (MM:SS)
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        // Fungsi untuk memulai countdown timer untuk meja
        function startCountdown(tableId, occupiedAt) {
            const timerElement = document.getElementById(`timer-${tableId}`);
            if (!timerElement) {
                console.error(`Elemen timer untuk meja ${tableId} tidak ditemukan`);
                return;
            }

            let occupiedTime;
            try {
                occupiedTime = new Date(occupiedAt).getTime();
                if (isNaN(occupiedTime)) {
                    throw new Error('Tanggal tidak valid');
                }
            } catch (error) {
                console.error(`occupied_at tidak valid untuk meja ${tableId}: ${occupiedAt}`, error);
                return;
            }

            const oneHour = 3600 * 1000; // 1 jam dalam milidetik
            const endTime = occupiedTime + oneHour;

            const updateTimer = () => {
                const now = new Date().getTime();
                const remaining = Math.max(0, Math.floor((endTime - now) / 1000));

                if (remaining > 0) {
                    timerElement.textContent = `Tersisa: ${formatTime(remaining)}`;
                    setTimeout(updateTimer, 1000);
                } else {
                    timerElement.textContent = '';
                    setAvailable(tableId); // Otomatis set ke available
                }
            };

            updateTimer();
        }

        // Inisialisasi timer untuk meja yang occupied setelah DOM dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                const tableId = row.id.replace('row-', '');
                const statusElement = row.querySelector(`#status-${tableId}`);
                const occupiedAt = row.dataset.occupiedAt;

                if (statusElement.textContent.trim().toUpperCase() === 'OCCUPIED' && occupiedAt) {
                    let timerElement = document.getElementById(`timer-${tableId}`);
                    if (!timerElement) {
                        timerElement = document.createElement('span');
                        timerElement.id = `timer-${tableId}`;
                        timerElement.className = 'ml-2 text-xs text-gray-600';
                        timerElement.setAttribute('aria-live', 'polite');
                        statusElement.parentElement.appendChild(timerElement);
                    }
                    try {
                        startCountdown(tableId, occupiedAt);
                    } catch (error) {
                        console.error(`Gagal memulai countdown untuk meja ${tableId}:`, error);
                    }
                }
            });
        });

        // Polling untuk pembaruan status meja secara real-time
        function pollTableStatus() {
            fetch('/tables', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(tables => {
                tables.forEach(table => {
                    const row = document.getElementById(`row-${table.id}`);
                    if (!row) return;

                    const statusBadge = row.querySelector(`#status-${table.id}`);
                    const timerCell = row.querySelector('td:nth-child(3)');
                    let timerElement = row.querySelector(`#timer-${table.id}`);

                    // Update status
                    statusBadge.textContent = table.status.toUpperCase();
                    statusBadge.className = `px-3 py-1.5 text-sm font-semibold rounded-full 
                        ${table.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
                    row.dataset.occupiedAt = table.occupied_at || '';

                    // Handle timer
                    if (table.status === 'occupied' && table.occupied_at) {
                        if (!timerElement) {
                            timerElement = document.createElement('span');
                            timerElement.id = `timer-${table.id}`;
                            timerElement.className = 'ml-2 text-xs text-gray-600';
                            timerElement.setAttribute('aria-live', 'polite');
                            timerCell.appendChild(timerElement);
                        }
                        startCountdown(table.id, table.occupied_at);
                    } else if (timerElement) {
                        timerElement.textContent = '';
                    }
                });
            })
            .catch(error => {
                console.error('Error saat polling status meja:', error);
            });
        }

        // Mulai polling setiap 10 detik
        setInterval(pollTableStatus, 10000);

        // Tambah Meja Otomatis
        async function createTable() {
            try {
                const response = await fetch("{{ route('table.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        auto_generate: true
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    const newRow = `
                        <tr id="row-${data.table.id}" class="bg-white border-b hover:bg-red-50" data-occupied-at="">
                            <td class="px-6 py-4 font-semibold">${document.querySelectorAll('#tableBody tr').length + 1}</td>
                            <td class="px-6 py-4 font-bold text-red-600">${data.table.number}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-green-100 text-green-800" id="status-${data.table.id}">
                                    AVAILABLE
                                </span>
                            </td>
                            <td class="px-6 py-4 space-x-2">
                                <button onclick="openEditModal('${data.table.id}', '${data.table.number}', 'available')"
                                    class="bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded-md text-sm text-white shadow">
                                    ✏️ Edit
                                </button>
                                <button onclick="setAvailable('${data.table.id}')"
                                    class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-md text-sm text-white shadow">
                                    ✅ Available
                                </button>
                                <button onclick="deleteTable('${data.table.id}')"
                                    class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md text-sm text-white shadow">
                                    🗑️ Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                    document.getElementById('tableBody').insertAdjacentHTML('beforeend', newRow);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Meja baru ditambahkan',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message
                });
            }
        }

        // Set Status Available
        async function setAvailable(id) {
            try {
                const response = await fetch(`/table/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'available',
                        occupied_at: null
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    const row = document.getElementById(`row-${id}`);
                    const statusBadge = row.querySelector(`#status-${id}`);
                    statusBadge.textContent = 'AVAILABLE';
                    statusBadge.className =
                        'px-3 py-1.5 text-sm font-semibold rounded-full bg-green-100 text-green-800';
                    const timerElement = document.getElementById(`timer-${id}`);
                    if (timerElement) timerElement.textContent = '';
                    row.dataset.occupiedAt = '';

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Status meja diubah menjadi AVAILABLE',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message
                });
            }
        }

        // Edit Meja
        function openEditModal(id, number, status) {
            document.getElementById('editId').value = id;
            document.getElementById('editNumber').value = number;
            document.getElementById('editStatus').value = status;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        document.getElementById('editForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('editId').value;
            const number = document.getElementById('editNumber').value;
            const status = document.getElementById('editStatus').value;

            try {
                const occupiedAt = status === 'occupied' ? new Date().toISOString() : null;
                const response = await fetch(`/table/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        number: number,
                        status: status,
                        occupied_at: occupiedAt
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    const row = document.getElementById(`row-${data.table.id}`);
                    row.querySelector('td:nth-child(2)').textContent = data.table.number;
                    const statusBadge = row.querySelector(`#status-${data.table.id}`);
                    statusBadge.textContent = data.table.status.toUpperCase();
                    statusBadge.className =
                        `px-3 py-1.5 text-sm font-semibold rounded-full 
                        ${data.table.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
                    row.dataset.occupiedAt = data.table.occupied_at || '';

                    // Handle timer for occupied status
                    const timerCell = row.querySelector('td:nth-child(3)');
                    let timerElement = row.querySelector(`#timer-${data.table.id}`);
                    if (data.table.status === 'occupied' && data.table.occupied_at) {
                        if (!timerElement) {
                            timerElement = document.createElement('span');
                            timerElement.id = `timer-${data.table.id}`;
                            timerElement.className = 'ml-2 text-xs text-gray-600';
                            timerElement.setAttribute('aria-live', 'polite');
                            timerCell.appendChild(timerElement);
                        }
                        startCountdown(data.table.id, data.table.occupied_at);
                    } else if (timerElement) {
                        timerElement.textContent = '';
                    }

                    closeEditModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data meja diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message
                });
            }
        });

        // Hapus Meja
        async function deleteTable(id) {
            Swal.fire({
                title: 'Hapus Meja?',
                text: "Data tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/table/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            document.getElementById(`row-${id}`).remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: 'Meja berhasil dihapus',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            throw new Error('Gagal menghapus meja');
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error.message
                        });
                    }
                }
            });
        }
    </script>
</x-app-layout>