<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'app_name',
        'app_logo',
        'courier_api_key',
        'courier_secret',
        'facebook_pixel_id',
        'order_note',
        'privacy_policy',
        'contact_name',
        'contact_phone',
        'contact_email',
        'contact_description',
        'contact_address',
        'about_us',
        'pathao_client_id',
        'pathao_client_secret',
        'pathao_access_token',
        'delivery_charge',
    ];
}
