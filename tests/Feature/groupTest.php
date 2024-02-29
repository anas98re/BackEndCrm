<?php

namespace Tests\Feature;

use App\Models\tsks_group;
use App\Models\users;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class groupTest extends TestCase
{

    // public function testBoxContents()
    // {
    //     info('Client');
    //     $box = [];

    //     $this->assertFalse(isset($box['toy']));
    //     $this->assertFalse(isset($box['ball']));
    // }

    // public function test2()
    // {
    //     $box = ['toy', 'torch', 'ball', 'cat', 'tissue'];

    //     $results = collect($box)->filter(function ($item) {
    //         return strpos($item, 't') === 0;
    //     })->toArray();

    //     $this->assertCount(3, $results);
    //     $this->assertContains('toy', $results);
    //     $this->assertContains('torch', $results);
    //     $this->assertContains('tissue', $results);

    //     // Empty array if no matches found
    //     $this->assertEmpty(collect($box)->filter(function ($item) {
    //         return strpos($item, 's') === 0;
    //     })->toArray());
    // }

    // public function testApigetallGroubs()
    // {
    //     // Send a GET request to the API endpoint
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer 13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465',
    //     ])->get('http://127.0.0.1:8000/api/getGroupsInfo');

    //     // Assert the response status code
    //     $response->assertStatus(200);

    //     $responseData = $response->json();

    //     $this->assertTrue($responseData['success']);

    //     // Get the data array from the response
    //     $data = $responseData['data'];
    //     $counts = tsks_group::count();
    //     $this->assertCount($counts, $data);
    // }

    // public function testAddGroup()
    // {
    //     $requestData = [
    //         'id_user' => 330,
    //         'groupName' => 'group 1',
    //         'description' => '..',
    //     ];

    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer 13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465',
    //     ])->post('/api/addGroup', $requestData);

    //     // Assert the response status code
    //     $response->assertStatus(200);

    //     $responseData = $response->json();

    //     // Assert the success property in the response data
    //     $this->assertTrue($responseData['success']);

    //     // Assert that the returned data matches the inserted group data
    //     $this->assertEquals($requestData['id_user'], $responseData['data']['created_by']);
    //     $this->assertEquals($requestData['groupName'], $responseData['data']['groupName']);
    //     $this->assertEquals($requestData['description'], $responseData['data']['description']);

    //     // Additional assertions and validation can be performed here
    // }
}
