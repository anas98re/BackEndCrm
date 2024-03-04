<?php

namespace Tests\Feature;

use App\Models\tsks_group;
use App\Models\users;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\MobileApp\Entities\company;
use Tests\TestCase;

class companyTest extends TestCase
{
    use WithFaker;

    public function testAddCommentToCompany()
    {
        $companyData = [
            'content' => $this->faker->paragraph,
            'id_user' => users::inRandomOrder()->first()->id_user,
            'date_comment' => $this->faker->dateTime->format('Y-m-d H:i:s')
        ];

        $fk_company = company::inRandomOrder()->first()->id_Company;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->post('/api/addCommentToCompany/' . $fk_company, $companyData);

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'fk_user',
                'fk_company',
                'content',
                'date_comment',
                'id_comment_company',
                'nameUser',
                'img_image'
            ],
            'message'
        ]);
    }
}
