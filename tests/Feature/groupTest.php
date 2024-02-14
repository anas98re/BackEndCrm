<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class groupTest extends TestCase
{

    public function test_example(): void
    {
        $response = $this->get('/getGroupsInfo');

        $response->assertStatus(200);
    }
}
