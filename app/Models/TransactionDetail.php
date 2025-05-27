<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'toping_id', 
        'quantity',
        'subtotal'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function toping()
    {
        return $this->belongsTo(Toping::class, 'toping_id');
    }

    public static function generateTransactionCode($customerName)
    {
        return DB::transaction(function () use ($customerName) {
            $datetime = Carbon::now()->format('YmdHis');
            $name = Str::upper(substr(preg_replace('/\s+/', '', $customerName), 0, 3));
            $name = str_pad($name, 3, 'X', STR_PAD_RIGHT);
            
            $lastTransaction = Transaction::whereDate('created_at', today())
                ->lockForUpdate()
                ->latest()
                ->first();

            $counter = $lastTransaction ? 
                intval(substr($lastTransaction->code, -4)) + 1 : 1;
            
            $counter = str_pad($counter, 4, '0', STR_PAD_LEFT);
            
            return "TRX-{$datetime}-{$name}-{$counter}";
        });
    }
}