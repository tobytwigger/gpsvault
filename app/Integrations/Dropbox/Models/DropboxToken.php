<?php

namespace App\Integrations\Dropbox\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropboxToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_token',
        'user_id'
    ];
}
