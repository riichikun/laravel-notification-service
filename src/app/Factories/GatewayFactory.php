<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracrs\GatewayServiceInterface;
use App\Enums\Channel;
use App\Services\Gateway\EmailGateway;
use App\Services\Gateway\SmsGateway;

final class GatewayFactory
{
    public function getGateway(Channel $channel): GatewayServiceInterface
    {
        return match($channel) {
            Channel::SMS => app(SmsGateway::class),
            Channel::EMAIL => app(EmailGateway::class),
        };
    }
}
