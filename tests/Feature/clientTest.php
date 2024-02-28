<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class clientTest extends TestCase
{

    public function testRescheduleOrCancelVisitClient()
    {
        info('Client');
        $bearerToken = '13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465';
        $requestDataCancel = [
            'typeProcess' => 'cancel',
            'processReason' => '..',
        ];
        $requestDataReschedule = [
            'typeProcess' => 'reschedule',
            'date_client_visit' => '2023-12-05 14:30:00',
            'processReason' => '..',
            'type_date' => '..',
            'date_end' => '2023-12-07 14:30:00',
        ];

        // Combine both sets of data into an array
        $requestDataArray = [$requestDataCancel, $requestDataReschedule];

        foreach ($requestDataArray as $requestData) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $bearerToken,
            ])->post('/api/rescheduleOrCancelVisitClient/222', $requestData);

            $response->assertStatus(200);

            $responseData = $response->json();
            info($responseData);
            info('ee' , array('e..0'));
            // Assert that the success flag is true
            $this->assertTrue($responseData['success']);

            // Assert that the message content is as expected
            $this->assertEquals('done', $responseData['message']);

            // Assert that the HTTP status code is 200
            // $this->assertEquals(200, $responseData->getStatusCode());
        }
    }
}
