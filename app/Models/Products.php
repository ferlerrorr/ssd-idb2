<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model

{
    /**
     * ! The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';



    //!Public Data
    protected $fillable = ['product_id', 'variant_id', 'sku_number', 'generic_name', 'level'];
    //!Hidden Data
    // protected $hidden = ['created_at', 'updated_at', 'id'];
}

/**
 * * The Product Model Handles Products Table in the Database
 */

 // ? Is It still in the process of expansion
