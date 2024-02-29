<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class clientDateTest extends TestCase
{

    // public function testRescheduleOrCancelVisitClient()
    // {
    //     info('Client');
    //     $requestDataCancel = [
    //         'typeProcess' => 'cancel',
    //         'processReason' => '..',
    //     ];
    //     $requestDataReschedule = [
    //         'typeProcess' => 'reschedule',
    //         'date_client_visit' => '2023-12-05 14:30:00',
    //         'processReason' => '..',
    //         'type_date' => '..',
    //         'date_end' => '2023-12-07 14:30:00',
    //     ];

    //     // Combine both sets of data into an array
    //     $requestDataArray = [$requestDataCancel, $requestDataReschedule];

    //     foreach ($requestDataArray as $requestData) {
    //         $response = $this->withHeaders([
    //             'Authorization' => 'Bearer ' . $this->bearerToken,
    //         ])->post('/api/rescheduleOrCancelVisitClient/222', $requestData);

    //         $response->assertStatus(200);

    //         $responseData = $response->json();
    //         info($responseData);
    //         info('ee' , array('e..0'));
    //         // Assert that the success flag is true
    //         $this->assertTrue($responseData['success']);

    //         // Assert that the message content is as expected
    //         $this->assertEquals('done', $responseData['message']);
    //         $this->assertEquals(200, $responseData['code']);

    //         $result = $response->decodeResponseJson()['message'];
    //         $this->assertEquals($result, $responseData['message']);
    //     }
    // }

    // public function testGetDateVisitAgentFromQuery()
    // {
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $this->bearerToken,
    //     ])->get('/api/getDateVisitAgent/1');
    //     $response->assertStatus(200);
    //     $responseData = $response->json();
    //     $this->assertTrue($responseData['success']);
    //     $result = $response->decodeResponseJson()['data'];
    //     // dd($result);
    //     $this->assertEquals($result, $responseData['data']);
    // }


}
