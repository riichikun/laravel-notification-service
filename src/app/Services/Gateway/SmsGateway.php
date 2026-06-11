<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Contracrs\GatewayServiceInterface;
use App\Enums\Status;
use App\Models\RecipientMessage;

final class SmsGateway implements GatewayServiceInterface
{
    public function send(string $id): bool {
        $message = RecipientMessage::find($id);

        if($message instanceof RecipientMessage) {
            $message->status = Status::DELIVERED;
            $message->save();

            return true;
        }

        return false;
    }
}
