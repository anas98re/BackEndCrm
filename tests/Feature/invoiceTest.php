<?php

namespace Tests\Feature;

use App\Models\client_invoice;
use App\Models\tsks_group;
use App\Models\users;
use CURLFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\MobileApp\Entities\company;
use Tests\TestCase;

class invoiceTest extends TestCase
{
    use WithFaker;

    public function testAddInvoice()
    {
        $data = [
            'fk_idClient' => '100',
            'fk_idUser' => '1',
            'fk_regoin_invoice' => '1',
            'address_invoice' => 'Hayden',
            'numbarnch' => '22',
            'nummostda' => '21',
            'numusers' => '2',
            'numTax' => '87',
            'ready_install' => '1',
            'currency_name' => 'USD',
            'comment' => 'qui-vitae-ea',
            'products' => [
                [
                    'fk_product' => '148',
                    'amount' => '15',
                    'price' => '15',
                    'taxtotal' => '34',
                    'rate_admin' => '2',
                    'rateUser' => '4',
                ]
            ],
            'fk_regoin' => '3',
            'fk_country' => '1',
            'nameUser' => 'fg',
            // 'file' => new CURLFile('/home/mustafa/Pictures/Screenshots/Screenshot from 2024-04-17 11-41-07.png'),
            // 'logo' => new CURLFile('/home/mustafa/Pictures/Screenshots/Screenshot from 2024-04-17 11-40-09.png'),
            // 'uploadfiles[0]' => new CURLFile('/home/mustafa/Pictures/Screenshots/Screenshot from 2024-04-17 14-32-50.png'),
            // 'uploadfiles[1]' => new CURLFile('/home/mustafa/Pictures/Screenshots/Screenshot from 2024-04-21 10-07-01.png')
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->post('/api/addInvoice', $data);

        $responseData = $response->json();
        $response->assertStatus(200);
        $response->assertJson(["result" => "success"]);
    }

    public function testUpdateInvoice()
    {
        $data = [
            'fk_idClient' => '100',
            'fk_idUser' => '1',
            'fk_regoin_invoice' => '1',
            'address_invoice' => 'Hayden',
            'numbarnch' => '22',
            'nummostda' => '21',
            'numusers' => '2',
            'numTax' => '87',
            'ready_install' => '1',
            'currency_name' => 'USD',
            'comment' => 'qui-vitae-ea',
            'products' => [
                [
                    'fk_product' => '148',
                    'amount' => '15',
                    'price' => '15',
                    'taxtotal' => '34',
                    'rate_admin' => '2',
                    'rateUser' => '4',
                ]
            ],
            'product_to_delete' => [
                150,
            ],
            'fk_regoin' => '3',
            'fk_country' => '1',
            'nameUser' => 'fg',
            // 'file' => new CURLFile('/home/mustafa/Pictures/Screenshots/Screenshot from 2024-04-17 11-41-07.png'),
            // 'logo' => new CURLFile('/home/mustafa/Pictures/Screenshots/Screenshot from 2024-04-17 11-40-09.png'),
            // 'uploadfiles[0]' => new CURLFile('/home/mustafa/Pictures/Screenshots/Screenshot from 2024-04-17 14-32-50.png'),
            // 'uploadfiles[1]' => new CURLFile('/home/mustafa/Pictures/Screenshots/Screenshot from 2024-04-21 10-07-01.png')
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->post('/api/updateInvoice/'. client_invoice::latest('id_invoice')->first()->id_invoice, $data);

        $responseData = $response->json();
        $response->assertStatus(200);
        $response->assertJson(["result" => "success"]);
    }
}
