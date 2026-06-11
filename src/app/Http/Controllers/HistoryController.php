<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RecipientMessage;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

final class HistoryController extends Controller
{
    public function get(string $id)
    {
        try {
            $id = Uuid::fromString($id);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'Invalid recipient uuid'], 422);
        }

        $RecipientMessage = RecipientMessage::where('recipient_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $RecipientMessage->toJson();
    }
}
