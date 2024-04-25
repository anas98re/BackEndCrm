<?php

namespace Tests\Feature;

use App\Models\client_communication;
use App\Models\client_invoice;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class clientCommunicationTest extends TestCase
{
    use WithFaker;

    public function testSetDateInstall()
    {
        $requestData = [
            'clientusername' => 'hello',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/setDateInstall/'.client_invoice::inRandomOrder()->first()->id_invoice, $requestData);

        $response->assertStatus(200);
        $response->assertJson(["result" => "success"]);
    }

    public function testUpdateCommunication()
    {
        $id_communication = client_communication::inRandomOrder()->first()->id_communication;

        $type_communcation = ['دوري', 'تركيب', 'ترحيب'];
        $type_communcationValue = array_rand($type_communcation);

        $updated = ['value', null];
        $updatedValue = array_rand($updated);

        $type = ['value', null];
        $typeValue = array_rand($type);

        $rate = [mt_rand(1, 5), '0.0'];
        $rateValue = array_rand($rate);

        $CommunicationData = [
            // 'id_communication' => $id_communication,
            'date_communication' => Carbon::now(),
            'type_communcation' => $type_communcationValue,
            'fk_user' => users::inRandomOrder()->first()->id_user,
            'result' => $this->faker->paragraph,
            'rate' => $rateValue,
            'number_wrong' => $this->faker->buildingNumber,
            'is_suspend' => mt_rand(0, 1),
            'isRecommendation' => mt_rand(0, 1),
            'is_visit' =>  mt_rand(0, 1),
            'type_install' => mt_rand(1, 2),
            'type' => $typeValue,
            'updated' => $updatedValue
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerTokenAnas,
        ])->postJson('/api/updateCommunication?id_communication=' . $id_communication, $CommunicationData);
        $response->assertStatus(200);

        $responseData = $response->json();
        $this->assertEquals($response->decodeResponseJson()['result'], 'success');
        $message = $response->decodeResponseJson()['message'];
        $this->assertEquals($message, $responseData['message']);
    }
}
