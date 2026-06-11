<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\RecipientMessage;
use Tests\TestCase;

class HistoryControllerTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/api/history/'.RecipientMessage::TEST_RECIPIENT_ID);

        $response->assertStatus(200);
    }
}
