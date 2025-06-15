<!DOCTYPE html>
<html>
<head>
    <title>Laporan Detail Transaksi - Warung Seblak Ajnira</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
        .letterhead { display: flex; align-items: center; padding: 15px 20px; background-color: #f8f8f8; border-bottom: 2px solid #e53e3e; margin-bottom: 20px; }
        .logo { width: 70px; height: 70px; object-fit: contain; margin-right: 15px; }
        .company-info h1 { margin: 0; font-size: 22px; color: #e53e3e; }
        .company-info p { margin: 3px 0; font-size: 13px; color: #555; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-header h2 { font-size: 20px; color: #333; margin: 0; }
        .report-header .date-range { font-size: 14px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #e53e3e; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .total-box { display: inline-block; margin-top: 20px; padding: 10px 15px; border: 1px solid #e53e3e; border-radius: 5px; }
        .total-label { font-weight: bold; font-size: 14px; color: #333; }
        .total-amount { font-size: 16px; color: #e53e3e; margin-top: 5px; }
        .signature { float: right; text-align: center; margin-top: 40px; width: 200px; }
        .signature-line { border-top: 1px solid #000; width: 150px; margin: 50px auto 5px; }
        .signature p { margin: 2px 0; font-size: 12px; }
        .no-data { text-align: center; font-size: 14px; color: #666; margin: 40px 0; }
        @media print {
            body { margin: 0; padding: 10mm; }
            .letterhead { position: fixed; top: 0; left: 0; right: 0; background: white; padding: 10px 20px; }
            .container { margin-top: 100px; }
            th { background-color: #e53e3e !important; color: white !important; -webkit-print-color-adjust: exact; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            .total-box { position: fixed; bottom: 100px; left: 20px; border: none; background: none; }
            .signature { position: fixed; bottom: 50px; right: 20px; }
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Warung Seblak Ajnira" class="logo">
        <div class="company-info">
            <h1>WARUNG SEBLAK AJNIRA</h1>
            <p>Jl. Raya Seblak No. 123, Kota Bandung</p>
            <p>Telp: (022) 1234-5678 | Email: ajnira@seblak.com</p>
        </div>
    </div>

    <div class="container">
        <div class="report-header">
            <h2>LAPORAN DETAIL TRANSAKSI</h2>
            <div class="date-range">
                Periode: 
                {{ $start_date ? date('d/m/Y', strtotime($start_date)) : '-' }}
                s/d
                {{ $end_date ? date('d/m/Y', strtotime($end_date)) : '-' }}
            </div>
        </div>

        @if($transactions->isEmpty())
            <div class="no-data">
                Tidak ada detail transaksi untuk periode ini.
            </div>
        @else
            @foreach($transactions as $transactionId => $items)
            @php
                $transaction = $items->first()->transaction;
                $total = $items->sum('subtotal');
            @endphp
            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 16px; margin: 0 0 10px;">
                    #{{ $transaction->code ?? 'N/A' }} | {{ ucfirst($transaction->status) }} | {{ $transaction->created_at->format('d/m/Y H:i') }}
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Toping</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->toping->name ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: right; font-weight: bold;">Total:</td>
                            <td style="font-weight: bold;">Rp{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endforeach

            <div class="total-box">
                <div class="total-label">TOTAL PENDAPATAN</div>
                <div class="total-amount">
                    Rp{{ number_format($transactions->sum(fn($items) => $items->sum('subtotal')), 0, ',', '.') }}
                </div>
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <p>(__________________________)</p>
                <p>Pimpinan Warung</p>
                <p>Warung Seblak Ajnira</p>
                <p>Tanggal: {{ date('d/m/Y') }}</p>
            </div>
        @endif
    </div>
</body>
</html>