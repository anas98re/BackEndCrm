<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class clientTest extends TestCase
{

    public function testEditClientByTypeClient()
    {
        $request1 = [
            'type_client' => 'عرض سعر',
            'offer_price' => '5',
            'date_price' => '5'
        ];
        $request2 = [
            'type_client' => 'تفاوض',
            'offer_price' => '5',
            'date_price' => '5'
        ];
        $request3 = [
            'type_client' => 'مستبعد',
            'reason_change' => 'reason',
        ];
        $allTypeRequests = [$request1, $request2, $request3];
        foreach ($allTypeRequests as $type) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
            ])->post('/api/editClientByTypeClient/1', $type);
            $this->assertTrue(in_array($type['type_client'], ['عرض سعر', 'تفاوض', 'مستبعد']));
            $response->assertStatus(200);
            $responseData = $response->json();
            $this->assertTrue($responseData['success']);
            $result = $response->decodeResponseJson()['data'];
            // dd($result);
            $this->assertEquals($result, $responseData['data']);
        }
    }

    public function testAppproveAdmin()
    {
        $request1 = [
            'isAppprove' => true,
        ];
        $request2 = [
            'isAppprove' => false,
        ];

        $allTypeRequests = [$request1, $request2];
        foreach ($allTypeRequests as $type) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
            ])->post('/api/clientAppproveAdmin/1', $type);
        }
        $this->assertTrue(in_array($type['isAppprove'], [1, 0]));
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $result = $response->decodeResponseJson()['data'];
        // dd($result);
        $this->assertEquals($result, $responseData['data']);
    }
}
