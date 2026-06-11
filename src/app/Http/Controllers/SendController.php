<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Jobs\SendNotificationJob;
use App\Models\RecipientMessage;
use App\Models\SentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

final class SendController extends Controller
{
    public function send(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'idempotency_key' => 'required|string|uuid',
            'channel' => 'required|in:sms,email',
            'message_text' => 'required|string',
            'priority' => 'required|in:transactional,marketing',
            'recipient_ids' => 'required|array|min:1',
            'recipient_ids.*' => 'required|string|uuid',
        ]);

        if ($validated->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validated->errors()], 422);
        }

        $data = $validated->getData();

        $redisKey = 'idempotency:'.$data['idempotency_key'];
        $lock = Cache::lock($redisKey, 30);

        if (false === $lock->get()) {
            return response()->json(
                ['status' => 'duplicate', 'message' => 'The request was already sent or is being processed'],
                200
            );
        }

        try {
            $existingRequest = SentRequest::where('idempotency_key', $data['idempotency_key'])->first();
            if ($existingRequest) {
                return response()->json(
                    [
                        'message' => 'The request was already sent or is being processed',
                        'request_id' => $existingRequest->idempotency_key
                    ],
                    200
                );
            }


            // Атомарное создание записей и постановка в очередь
            $result = DB::transaction(function () use ($data) {
                $notificationRequest = SentRequest::create(['idempotency_key' => $data['idempotency_key']]);

                foreach ($data['recipient_ids'] as $recipientId) {
                    $notification = RecipientMessage::create([
                        'notification_request_id' => $data['idempotency_key'],
                        'recipient_id' => $recipientId,
                        'status' => Status::QUEUED,
                        'channel' => $data['channel'],
                        'message_text' => $data['message_text'],
                    ]);


                    // Приоритизация трафика через разные очереди (high / default)
                    $queue = $data['priority'] === 'transactional' ? 'high' : 'default';


                    SendNotificationJob::dispatch($notification->id)->onQueue($queue);
                }

                return $notificationRequest;
            });


            Cache::put($redisKey, 'processed', now()->addDay());

            return response()->json(
                ['message' => 'Request was successfully processed', 'request_id' => $result->idempotency_key],
                200
            );
        } catch (Throwable $e) {
            /*
             * Если БД или очередь упали — снимаем временную блокировку в Redis, чтобы пользователь мог безопасно
             * повторить запрос
             */
            $lock->release();

            Log::error('Notification processing failed: ' . $e->getMessage());

            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
