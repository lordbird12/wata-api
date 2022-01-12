<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFiles extends Model
{
    use HasFactory;

    protected $hidden = ['deleted_at'];

    //////////////////////////////////////// format //////////////////////////////////////

    public function getPathAttribute($value)
    {
        return ($value ? url($value) : null);
    }
    
    //////////////////////////////////////// relation //////////////////////////////////////

    public function product()
    {
        return $this->belongsTo(Products::class,'id','product_id');
    }
}
