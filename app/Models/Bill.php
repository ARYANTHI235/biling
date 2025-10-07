<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['bill_no', 'customer_name', 'total_amount'];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }
}
