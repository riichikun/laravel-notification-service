<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class RecipientMessage extends Model
{
    use HasUuids;

    public $incrementing = false;

    // 4. Set the primary key type to string
    protected $keyType = 'string';

    const string TEST_RECIPIENT_ID = '019eb099-c561-7f82-ae8c-3f4fec416b77';

    protected $fillable = ['notification_request_id', 'recipient_id', 'status', 'channel', 'message_text'];
}
