<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\Channel;
use App\Enums\Status;
use App\Factories\GatewayFactory;
use App\Models\RecipientMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

final class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $messageId) {}

    /**
     * Execute the job.
     */
    public function handle(GatewayFactory $GatewayFactory): void
    {;
        $message = RecipientMessage::find($this->messageId);

        if($message instanceof RecipientMessage) {
            $message->status = Status::SENT;
            $message->save();

            $GatewayFactory
                ->getGateway(Channel::from($message->channel))
                ->send($message->id);
        }
    }
}
