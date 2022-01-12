<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drug extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $softDelete = true;

    protected $hidden = ['deleted_at'];


    //////////////////////////////////////// relation //////////////////////////////////////

    public function drug_files()
    {
        return $this->hasMany(DrugFiles::class, 'drug_id', 'id');
    }
}
