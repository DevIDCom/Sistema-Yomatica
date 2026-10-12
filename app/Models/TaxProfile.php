<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxProfile extends Model {
    public $timestamps = false;
    protected $fillable = ['tax_id', 'legal_name', 'tax_regime', 'zip_code', 'facturapi_api_key_encrypted'];

    protected $casts = [
        'facturapi_api_key_encrypted' => 'encrypted',
    ];
}