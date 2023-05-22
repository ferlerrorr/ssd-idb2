<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rbac extends Model
{

    protected $table = 'rbac';
    //!Public Data
    protected $fillable = ['id', 'rbac_permission', 'created_at', 'updated_at'];
    // //!Hidden Data
    protected $hidden = ['id', 'created_at', 'updated_at'];
}
