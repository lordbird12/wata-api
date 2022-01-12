<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugFiles extends Model
{
    use HasFactory;

    protected $hidden = ['deleted_at'];

    //////////////////////////////////////// format //////////////////////////////////////

    public function getPathAttribute($value)
    {
        return ($value ? url($value) : null);
    }

    //////////////////////////////////////// relation //////////////////////////////////////

    public function drug()
    {
        return $this->belongsTo(Drugs::class, 'id', 'drug_id');
    }
}
