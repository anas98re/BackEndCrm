<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class clientTest extends TestCase
{

    public function testRescheduleOrCancelVisitClient()
    {
        // Prepare the request data for rescheduling the visit
        $requestDataReschedule = [
            'typeProcess' => 'reschedule',
            'date_client_visit' => '2023-12-05 14:30:00',
            'processReason' => '..',
            'type_date' => '..',
            'date_end' => '2023-12-07 14:30:00',
        ];

        // Send a request to reschedule the visit
        $responseReschedule = $this->withHeaders([
            'Authorization' => 'Bearer 13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465',
        ])->post('/api/rescheduleOrCancelVisitClient/1', $requestDataReschedule);

        // Assert that the rescheduling request was successful
        $responseReschedule->assertStatus(200);

        // Prepare the request data for canceling the visit
        $requestDataCancel = [
            'typeProcess' => 'cancel',
            'processReason' => '..',
        ];

        // Send a request to cancel the visit
        $responseCancel = $this->withHeaders([
            'Authorization' => 'Bearer 13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465',
        ])->post('/api/rescheduleOrCancelVisitClient/1', $requestDataCancel);

        // Assert that the cancellation request was successful
        $responseCancel->assertStatus(200);
    }
}
