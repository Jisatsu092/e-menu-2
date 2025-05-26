<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi - Warung Seblak Ajnira</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .letterhead {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background-color: #f8f8f8;
            border-bottom: 2px solid #e53e3e;
            margin-bottom: 20px;
        }
        .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 15px;
        }
        .company-info h1 {
            margin: 0;
            font-size: 22px;
            color: #e53e3e;
        }
        .company-info p {
            margin: 3px 0;
            font-size: 13px;
            color: #555;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-header h2 {
            font-size: 20px;
            color: #333;
            margin: 0;
        }
        .report-header .date-range {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #e53e3e;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .total-box {
            margin-top: 20px;
            padding: 10px 15px;
            border: 1px solid #e53e3e;
            border-radius: 5px;
            background-color: #fff;
            display: inline-block;
        }
        .total-label {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }
        .total-amount {
            font-size: 16px;
            color: #e53e3e;
            margin-top: 5px;
        }
        .signature {
            float: right;
            text-align: center;
            margin-top: 40px;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 150px;
            margin: 50px auto 5px;
        }
        .signature p {
            margin: 2px 0;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 60px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin: 40px 0;
        }
        @media print {
            body {
                margin: 0;
                padding: 10mm;
            }
            .letterhead {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: white;
                padding: 10px 20px;
                border-bottom: 2px solid #e53e3e;
            }
            .container {
                margin-top: 100px;
            }
            th {
                background-color: #e53e3e !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .total-box {
                position: fixed;
                bottom: 100px;
                left: 20px;
                border: none;
                background: none;
            }
            .signature {
                position: fixed;
                bottom: 50px;
                right: 20px;
            }
            .footer {
                position: fixed;
                bottom: 20px;
                width: 100%;
                left: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <div class="letterhead">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Warung Seblak Ajnira" class="logo">
        <div class="company-info">
            <h1>WARUNG SEBLAK AJNIRA</h1>
            <p>Jl. Raya Seblak No. 123, Kota Bandung</p>
            <p>Telp: (022) 1234-5678 | Email: ajnira@seblak.com</p>
        </div>
    </div>

    <!-- Isi Laporan -->
    <div class="container">
        <div class="report-header">
            <h2>LAPORAN TRANSAKSI</h2>
            <div class="date-range">
                Periode: 
                {{ request('start') ? date('d/m/Y', strtotime(request('start'))) : '-' }}
                s/d
                {{ request('end') ? date('d/m/Y', strtotime(request('end'))) : '-' }}
            </div>
        </div>

        @if ($transactions->isEmpty())
            <div class="no-data">
                Tidak ada transaksi untuk periode ini.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>User</th>
                        <th>Meja</th>
                        <th>Total Harga Beli</th>
                        <th>Pendapatan Kotor</th>
                        <th>Pendapatan Bersih</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPrice = 0; // Total Harga Beli
                        $grossIncome = 0; // Pendapatan Kotor
                        $netIncome = 0; // Pendapatan Bersih
                    @endphp
                    @foreach ($transactions->where('status', '!=', 'cancelled') as $transaction)
                        @php
                            // Hitung per transaksi
                            $transactionPrice = $transaction->details->sum(function ($detail) {
                                return $detail->toping->price * $detail->quantity;
                            });
                            $transactionPriceBuy = $transaction->details->sum(function ($detail) {
                                return $detail->toping->price_buy * $detail->quantity;
                            });
                            $transactionNet = $transactionPriceBuy - $transactionPrice;

                            $totalPrice += $transactionPrice;
                            $grossIncome += $transaction->total_price; // Berdasarkan total_price
                            $netIncome += $transactionNet;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->user->name ?? '-' }}</td>
                            <td>{{ $transaction->table->number ?? 'Takeaway' }}</td>
                            <td>Rp{{ number_format($transactionPrice, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($transactionNet, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($transaction->status) }}</td>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-box">
                <div class="total-label" style="margin-top: 10px;">PENDAPATAN KOTOR</div>
                <div class="total-amount">
                    Rp{{ number_format($grossIncome, 0, ',', '.') }}
                </div>
                <div class="total-label" style="margin-top: 10px;">TOTAL HARGA BELI</div>
                <div class="total-amount">
                    Rp{{ number_format($totalPrice, 0, ',', '.') }}
                </div>
                <div class="total-label" style="margin-top: 10px;">PENDAPATAN BERSIH</div>
                <div class="total-amount">
                    Rp{{ number_format($netIncome, 0, ',', '.') }}
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

        <div class="footer">
            Laporan ini dicetak oleh sistem Warung Seblak Ajnira | www.seblak-ajnira.id
        </div>
    </div>
</body>
</html>