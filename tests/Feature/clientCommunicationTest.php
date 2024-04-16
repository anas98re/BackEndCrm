<?php

namespace Tests\Feature;

use App\Models\client_invoice;
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
        ])->postJson('/api/setDateInstall/4690', $requestData);

        $response->assertStatus(200);
        $response->assertJson(["result" => "success"]);
    }
}
