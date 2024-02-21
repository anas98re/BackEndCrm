<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class taskTest extends TestCase
{
    public function testAddTask()
    {
        $requestData = [
            'title' => 'Task Title',
            'description' => 'Task Description',
            'created_by' => 1,
            'assigned_by' => 2,
            'assigned_to' => 3,
            'client_id' => 4,
            'invoice_id' => 5,
            'group_id' => 6,
            'fk_region' => 7,
            'assigend_department_from' => 1,
            'assigend_department_to' => 1,
            'assigend_region_from' => 1,
            'assigend_region_to' => 1,
            'assignment_type_from' => 1,
            'assignment_type_to' => 1,
            'public_Type' => 'Public Type',
            'main_type_task' => 'ProccessAuto',
            'recive_date' => '2023-12-05 14:30:00',
            'start_date' => '2023-12-05 14:30:00',
            'deadline' => '2023-12-05 14:30:00',
            'hours' => 10,
            'completion_percentage' => 0,
            'recurring' => 0,
            'dateTimeCreated' => '2023-12-05 14:30:00',
            'recurring_type' => null,
            'actual_delivery_date' => '2023-12-05 14:30:00',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer 13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465',
        ])->post('/api/addTask', $requestData);
        info($response->getContent());
        $response->assertStatus(200);
    }
}
