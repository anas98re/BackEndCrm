<?php

namespace Tests\Feature;

use App\Models\agent;
use App\Models\clients;
use App\Models\clients_date;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class clientDateTest extends TestCase
{
    use WithFaker;

    public function testRescheduleOrCancelVisitClient()
    {
        $requestDataCancel = [
            'typeProcess' => 'cancel',
            'processReason' => $this->faker->sentence,
        ];
        $requestDataReschedule = [
            'typeProcess' => 'reschedule',
            'date_client_visit' => $this->faker->dateTime->format('Y-m-d H:i:s'),
            'processReason' => $this->faker->sentence,
            'type_date' => $this->faker->sentence,
            'date_end' => $this->faker->dateTime->format('Y-m-d H:i:s'),
        ];

        // Combine both sets of data into an array
        $requestDataArray = [$requestDataCancel, $requestDataReschedule];
        $idclients_date = clients_date::inRandomOrder()->first()->idclients_date;
        foreach ($requestDataArray as $requestData) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
            ])->post('/api/rescheduleOrCancelVisitClient/'.$idclients_date, $requestData);

            $response->assertStatus(200);

            $responseData = $response->json();
            $this->assertTrue($responseData['success']);


            $this->assertEquals('done', $responseData['message']);
            $this->assertEquals(200, $responseData['code']);
            $result = $response->decodeResponseJson()['message'];
            $this->assertEquals($result, $responseData['message']);
        }
    }

    public function testGetDateVisitAgentFromQuery()
    {
        $agent_id = agent::inRandomOrder()->first()->id_agent;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->get('/api/getDateVisitAgent/'.$agent_id);
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);
    }

    public function testUpdateStatusForVisit()
    {
        $requestData = [
            // 'id_clients' => clients::inRandomOrder()->first()->id_clients,
            'is_done' => 1,
            'comment' => $this->faker->sentence,
            'agent_id' => agent::inRandomOrder()->first()->id_agent,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/updateStatusForVisit/'.clients_date::inRandomOrder()->first()->idclients_date, $requestData);

        $response->assertStatus(200);
        $response->assertJson(["result" => "success"]);
    }

}
