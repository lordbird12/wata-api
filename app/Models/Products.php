<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $softDelete = true;

    protected $hidden = ['deleted_at'];

     //////////////////////////////////////// format //////////////////////////////////////

    //  public function getStdTimeAttribute($value)
    //  {
    //      return ($value ? number_format($value, 2) : 0.00);
    //  }
 
    //  public function getStatusAtAttribute($value)
    //  {
    //      return ($value ? date('d/m/Y H:i:s', strtotime($value)) : null);
    //  }
 
    //  protected function serializeDate(DateTimeInterface $date)
    //  {
    //      return $date->format('d/m/Y H:i:s');
    //  }
 
     //////////////////////////////////////// relation //////////////////////////////////////
 
     public function product_files()
     {
         return $this->hasMany(ProductFiles::class,'product_id','id');
     }

}
