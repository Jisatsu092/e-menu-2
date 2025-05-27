<x-app-layout>
    <style>
        summary::-webkit-details-marker { display: none; }
        summary { list-style: none; }
        .print-button { background-color: #e53e3e; }
        .print-button:hover { background-color: #c53030; }
        .filter-button { background-color: #3b82f6; }
        .filter-button:hover { background-color: #2563eb; }
        .details-open summary span:last-child { transform: rotate(180deg); }
        .no-data { text-align: center; color: #666; padding: 20px; font-size: 16px; }
        .status-badge { padding: 4px 8px; border-radius: 4px; color: white; font-size: 12px; }
        .status-pending { background-color: #f59e0b; }
        .status-proses { background-color: #3b82f6; }
        .status-paid { background-color: #10b981; }
        .status-cancelled { background-color: #ef4444; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header Section -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <h2 class="text-2xl font-bold text-gray-800">Daftar Detail Transaksi</h2>
                        
                        <!-- Filter and Actions -->
                        @can('role-A')
                        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                            <form action="{{ route('transaction_details.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded border-gray-300">
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded border-gray-300">
                                <button type="submit" class="filter-button text-white px-4 py-2 rounded">
                                    Filter
                                </button>
                            </form>
                            <a href="{{ route('transaction_details.report', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
                               target="_blank" 
                               class="print-button text-white px-4 py-2 rounded text-center">
                                Cetak Laporan
                            </a>
                            
                            
                            <form action="{{ route('transaction_details.destroyAll') }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus semua data?')" 
                                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 w-full md:w-auto">
                                    Hapus Semua
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>

                    <!-- No Data Message -->
                    @if($details->isEmpty())
                        <div class="no-data">
                            Tidak ada detail transaksi yang ditemukan.
                        </div>
                    @else
                        <!-- Desktop View -->
                        <div class="hidden md:block">
                            @foreach($details as $transactionId => $items)
                            @php
                                $transaction = $items->first()->transaction ?? null;
                                $total = $items->sum('subtotal');
                            @endphp
                            
                            <details class="border rounded-lg mb-4 shadow-sm">
                                <summary class="bg-gray-100 p-4 cursor-pointer flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <span class="font-semibold">#{{ $transaction->code ?? 'N/A' }}</span>
                                        <span class="status-badge status-{{ $transaction->status ?? 'unknown' }}">
                                            {{ ucfirst($transaction->status ?? 'Unknown') }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ $items->count() }} Item | Total: Rp{{ number_format($total, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <span class="transform transition-transform duration-300 text-blue-600">▼</span>
                                </summary>

                                <div class="p-4 bg-gray-50">
                                    <div class="grid grid-cols-12 gap-4 font-medium mb-2 text-gray-700">
                                        <div class="col-span-5">Toping</div>
                                        <div class="col-span-2">Qty</div>
                                        <div class="col-span-3">Subtotal</div>
                                        <div class="col-span-2">Tanggal</div>
                                    </div>
                                    
                                    @foreach($items as $item)
                                    <div class="grid grid-cols-12 gap-4 py-2 border-t text-sm">
                                        <div class="col-span-5">{{ $item->toping->name ?? '-' }}</div>
                                        <div class="col-span-2">{{ $item->quantity ?? 0 }}</div>
                                        <div class="col-span-3">Rp{{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</div>
                                        <div class="col-span-2">{{ $transaction ? $transaction->created_at->format('d/m/Y') : '-' }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </details>
                            @endforeach
                        </div>

                        <!-- Mobile View -->
                        <div class="md:hidden space-y-4">
                            @foreach($details as $transactionId => $items)
                            @php
                                $transaction = $items->first()->transaction ?? null;
                                $total = $items->sum('subtotal');
                            @endphp
                            
                            <details class="border rounded-lg shadow-sm">
                                <summary class="p-4 bg-gray-100 flex justify-between items-center">
                                    <div>
                                        <div class="font-semibold">#{{ $transaction->code ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600 mt-1 flex gap-2 items-center">
                                            <span class="status-badge status-{{ $transaction->status ?? 'unknown' }}">
                                                {{ ucfirst($transaction->status ?? 'Unknown') }}
                                            </span>
                                            <span>{{ $items->count() }} Item | Rp{{ number_format($total, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <span class="text-blue-600">▼</span>
                                </summary>

                                <div class="p-4 space-y-3">
                                    @foreach($items as $item)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="font-medium">{{ $item->toping->name ?? '-' }}</p>
                                        <div class="flex justify-between text-sm mt-1">
                                            <span>Qty: {{ $item->quantity ?? 0 }}</span>
                                            <span class="text-green-600">
                                                Rp{{ number_format($item->subtotal ?? 0, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            Tanggal: {{ $transaction ? $transaction->created_at->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </details>
                            @endforeach
                        </div>
                    @endif

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $details->appends(['start_date' => request('start_date'), 'end_date' => request('end_date')])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('details').forEach(detail => {
            detail.addEventListener('toggle', () => {
                detail.classList.toggle('details-open', detail.open);
            });
        });
    </script>
</x-app-layout>