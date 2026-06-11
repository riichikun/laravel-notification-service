<?php

declare(strict_types=1);

namespace App\Contracrs;

interface GatewayServiceInterface
{
    public function send(string $id): bool;
}
