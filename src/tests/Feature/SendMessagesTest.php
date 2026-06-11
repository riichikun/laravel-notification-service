<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\RecipientMessage;
use App\Models\SentRequest;
use Symfony\Component\Uid\Uuid;
use Tests\TestCase;

final class SendMessagesTest extends TestCase
{
    public function test_send_messages_successful(): void
    {
        $this->withoutExceptionHandling();
        config(['queue.default' => 'sync']);

        $idempotencyKey = (string) Uuid::v7();


        /** Отправляем запрос впервые */
        $response = $this->post('/api/send', [
            'idempotency_key' => $idempotencyKey,
            'channel' => 'email',
            'message_text' => 'test text',
            'priority' => 'transactional',
            'recipient_ids' => [RecipientMessage::TEST_RECIPIENT_ID],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Request was successfully processed']);

        $this->assertDatabaseHas(RecipientMessage::class, [
            'notification_request_id' => $idempotencyKey,
            'status' => 'delivered'
        ]);


        /** Отправляем запрос повторно */
        $response = $this->post('/api/send', [
            'idempotency_key' => $idempotencyKey,
            'channel' => 'email',
            'message_text' => 'test text',
            'priority' => 'transactional',
            'recipient_ids' => [RecipientMessage::TEST_RECIPIENT_ID],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'The request was already sent or is being processed']);


        $SentRequest = SentRequest::where(['idempotency_key' => $idempotencyKey])->get()->first();
        self::assertInstanceOf(SentRequest::class, $SentRequest);

        $RecipientMessage = RecipientMessage::where(['notification_request_id' => $idempotencyKey])->get()->first();
        $RecipientMessage = $RecipientMessage->fresh();
        self::assertInstanceOf(RecipientMessage::class, $RecipientMessage);
    }


    public function test_send_messages_invalid_data(): void
    {
        $idempotencyKey = (string) Uuid::v7();


        /** Отправляем запрос с некорректными данными */
        $response = $this->post('/api/send', ['idempotency_key' => $idempotencyKey]);

        $response->assertStatus(422);
    }
}
