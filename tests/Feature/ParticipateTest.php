<?php

namespace Tests\Feature;

use App\Models\commentParticipate;
use App\Models\participate;
use App\Models\tsks_group;
use App\Models\users;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipateTest extends TestCase
{
    use WithFaker;

    public function testGetParticipateClints()
    {
        $ParticipateId = participate::inRandomOrder()->first()->id_participate;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->get('/api/getParticipateClints/' . $ParticipateId);

        // Assert the status code
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);
        // Assert the structure of the JSON response
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id_clients',
                    'name_client',
                    'name_enterprise',
                    'type_client',
                    'fk_regoin',
                    'fk_user',
                    'offer_price',
                    'date_price',
                    'date_create',
                    'tag',
                    'nameCountry',
                    'name_regoin',
                    'nameUser',
                    'fk_country'
                ]
            ],
            'message'
        ]);
    }

    public function testGetParticipateInvoices()
    {
        $ParticipateId = participate::inRandomOrder()->first()->id_participate;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->get('/api/getParticipateInvoices/' . $ParticipateId);

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);

        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id_invoice',
                    'date_create',
                    'fk_idClient',
                    'fk_idUser',
                    'notes',
                    'total',
                    'dateinstall_done',
                    'date_approve',
                    'address_invoice',
                    'invoice_source',
                    'amount_paid',
                    'renew_year',
                    'Date_FApprove',
                    'stateclient',
                    'approve_back_done',
                    'isApprove',
                    'name_regoin',
                    'currency_name',
                    'nameCountry',
                    'nameUser',
                    'fk_country',
                    'name_enterprise',
                    'name_client'
                ]
            ],
            'message'
        ]);
    }

    public function testAddCommentParticipate()
    {
        $clientData = [
            'content' => $this->faker->paragraph,
            'participate_id' => participate::inRandomOrder()->first()->id_participate
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->post('/api/addCommentParticipate', $clientData);

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'participate_id',
                'content',
                'date_comment',
                'user_id',
                'id',
                'nameUser',
                'img_image',
                'add_date',
                'update_date',
            ],
            'message',
        ]);
    }

    public function testGetParticipateComments()
    {
        $participate_id = commentParticipate::inRandomOrder()->first()->participate_id;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->get('/api/getParticipateComments/' . $participate_id);

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);

        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'participate_id',
                    'content',
                    'date_comment',
                    'user_id',
                    'id',
                    'nameUser',
                    'img_image',
                    'add_date',
                    'update_date',
                ],
            ],
            'message',
        ]);
    }
}
