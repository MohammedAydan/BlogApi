<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPremission extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "premission_id"
    ];
}
