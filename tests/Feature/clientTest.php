<?php

namespace Tests\Feature;

use App\Models\clients;
use App\Models\User;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class clientTest extends TestCase
{
    use WithFaker;

    public function testEditClientByTypeClient()
    {
        $request1 = [
            'type_client' => 'عرض سعر',
            'offer_price' => $this->faker->buildingNumber,
            'date_price' => $this->faker->dateTime->format('Y-m-d H:i:s')
        ];
        $request2 = [
            'type_client' => 'تفاوض',
            'offer_price' => $this->faker->buildingNumber,
            'date_price' => $this->faker->dateTime->format('Y-m-d H:i:s')
        ];
        $request3 = [
            'type_client' => 'مستبعد',
            'reason_change' => $this->faker->sentence,
        ];
        $allTypeRequests = [$request1, $request2, $request3];

        $client_id = clients::inRandomOrder()->first()->id_clients;

        foreach ($allTypeRequests as $type) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
            ])->post('/api/editClientByTypeClient/' . $client_id, $type);
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
        $client_id = clients::inRandomOrder()->first()->id_clients;
        $allTypeRequests = [$request1, $request2];
        foreach ($allTypeRequests as $type) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
            ])->post('/api/clientAppproveAdmin/' . $client_id, $type);
        }
        $this->assertTrue(in_array($type['isAppprove'], [1, 0]));
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);
    }

    public function testAddClient()
    {
        $size_activity = ['متوسط', 'كبير', 'صغير'];
        $size_activityValue = array_rand($size_activity);
        $clientData = [
            'name_client' => $this->faker->paragraph,
            'name_enterprise' => $this->faker->paragraph,
            'address_client' => $this->faker->paragraph,
            'mobile' => $this->faker->buildingNumber,
            'type_job' => $this->faker->paragraph,
            'city' => $this->faker->paragraph,
            'location' => $this->faker->paragraph,
            'date_create' => '2024-01-16',
            'fk_user' =>  users::inRandomOrder()->first()->id_user,
            'date_transfer' => $this->faker->dateTime->format('Y-m-d H:i:s'),
            'phone' => $this->faker->dateTime->format('Y-m-d H:i:s'),
            'email' => $this->faker->safeEmail,
            'size_activity' => $size_activityValue,
            'descActivController' => $this->faker->paragraph,
            'id_user' =>  Users::inRandomOrder()->first()->id_user,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/addClient', $clientData);

        $response->assertStatus(200);

        // Assert the response contains the expected message
        $response->assertJson([
            "result" => "success"
        ]);
    }

    public function testSimilarClientsNames()
    {
        $requestData = [
            'name_client' => $this->faker->paragraph,
            'name_enterprise' => $this->faker->paragraph,
            'phone' => $this->faker->buildingNumber
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/SimilarClientsNames', $requestData);

        $response->assertStatus(200);

        // Assert the response contains the expected message
        $response->assertJsonStructure([
            '*' => [
                'name_client',
                'name_enterprise',
                'phone',
                'id_clients',
                'date_create',
                'SerialNumber'
            ]
        ]);
    }

    public function testTransferClient()
    {
        $requestData = [
            'fk_user' => users::inRandomOrder()->first()->id_user,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/transferClient/'.clients::inRandomOrder()->first()->id_clients, $requestData);

        $response->assertStatus(200);
        $response->assertJson(["result" => "success"]);
    }

    public function testApproveOrRefuseTransferClient()
    {
        $requestData = [
            'approve' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/approveOrRefuseTransferClient/'.clients::whereNotNull('fkusertrasfer')->inRandomOrder()->first()->id_clients, $requestData);

        $response->assertStatus(200);
        $response->assertJson(["result" => "success"]);
    }
}
