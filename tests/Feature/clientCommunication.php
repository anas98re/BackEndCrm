<?php

namespace Tests\Feature;

use App\Models\client_invoice;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class clientCommunication extends TestCase
{
    use WithFaker;

    public function testSetDateInstall()
    {
        $requestData = [];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/setDateInstall/'.client_invoice::whereNotNull('fk_idUser')->inRandomOrder()->first()->id_invoice, $requestData);

        $response->assertStatus(200);
        $response->assertJson(["result" => "success"]);
    }
}
