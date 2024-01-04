<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{

    /**
     * ! The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';


    //!Public Data
    // protected $fillable = ['customer_name','gender','date_of_birth','email','contact_number','address','draft_order','doctor_details','provider'];
    protected $fillable = ['order'];
    protected $casts = [
        'order' => 'array'
    ];
}

/**
 * * The Order Model Handles Order Inputs to the Database
 */
 // ? Is the model has to be Time Stamped
