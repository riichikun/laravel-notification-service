<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class SentRequest extends Model
{
    public $incrementing = false;

    protected $fillable = ['idempotency_key'];
}
