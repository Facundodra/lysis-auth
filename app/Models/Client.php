<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Models\User;


class Client extends Model
{
    use HasFactory;

    public function user() {
        return $this->hasOne(User::class);
    }

    public function subscription() {
        return $this->hasOne(Subscription::class);
    }

    public function subscriptionType() {
        return $this->hasOneThrough(SubscriptionType::class, Subscription::class);
    }
}
