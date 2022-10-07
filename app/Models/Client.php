<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;
use App\Models\User;


class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'surname',
        'birth_date',
        'country_id'
    ];

    public function user() {
        return $this->hasOne(User::class);
    }

    public function subscription() {
        return $this->belongsTo(Subscription::class);
    }
}
