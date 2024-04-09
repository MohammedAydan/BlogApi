<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountConfirmation extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "accounts_confirmations";

    protected $fillable = [
        'owner_id',
        'full_name_in_arabic',
        'full_name_in_english',
        'id_card_front',
        'id_card_back',
        'status',
    ];
}
