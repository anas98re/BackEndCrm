<?php

namespace Tests\Feature;

use App\Models\client_invoice;
use App\Models\clients;
use App\Models\managements;
use App\Models\regoin;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\users;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class taskTest extends TestCase
{
    use WithFaker;
    
    public function testAddTask()
    {
        $assignmentTypes = ['user', 'department', 'region'];
        $randomAssignmentTypeKey = array_rand($assignmentTypes);
        $randomAssignmentType = $assignmentTypes[$randomAssignmentTypeKey];

        $main_type_task = ['ProccessAuto', 'ProcessManual', 'New', 'Updated'];
        $main_type_taskKey = array_rand($main_type_task);
        $main_type_taskType = $main_type_task[$main_type_taskKey];

        $title = $this->faker->sentence;
        $description = $this->faker->paragraph;
        $created_by = users::inRandomOrder()->first()->id_user;
        $assigned_by = users::inRandomOrder()->first()->id_user;
        $assigned_to = users::inRandomOrder()->first()->id_user;
        $client_id = clients::inRandomOrder()->first()->id_clients;
        $invoice_id = client_invoice::inRandomOrder()->first()->id_invoice;
        $fk_region = regoin::inRandomOrder()->first()->id_regoin;
        $assigend_department_from = managements::inRandomOrder()->first()->idmange;
        $assigend_department_to = managements::inRandomOrder()->first()->idmange;
        $assigend_region_from = regoin::inRandomOrder()->first()->id_regoin;
        $assigend_region_to = regoin::inRandomOrder()->first()->id_regoin;
        $public_Type = $this->faker->sentence;
        $recive_date = $this->faker->dateTime->format('Y-m-d H:i:s');
        $start_date = $this->faker->dateTime->format('Y-m-d H:i:s');
        $deadline = $this->faker->dateTime->format('Y-m-d H:i:s');
        $hours = $this->faker->buildingNumber;
        $completion_percentage = 0;
        $recurring = 0;
        $dateTimeCreated = $this->faker->dateTime->format('Y-m-d H:i:s');
        $recurring_type = null;
        $actual_delivery_date = $this->faker->dateTime->format('Y-m-d H:i:s');
        $updated_at = $this->faker->dateTime->format('Y-m-d H:i:s');
        $created_at = $this->faker->dateTime->format('Y-m-d H:i:s');
        $id = $this->faker->buildingNumber;
        $requestData = [
            'title' => $title,
            'description' => $description,
            'created_by' => $created_by,
            'assigned_by' => $assigned_by,
            'assigned_to' => $assigned_to,
            'client_id' => $client_id,
            'invoice_id' => $invoice_id,
            'fk_region' => $fk_region,
            'assigend_department_from' => $assigend_department_from,
            'assigend_department_to' => $assigend_department_to,
            'assigend_region_from' => $assigend_region_from,
            'assigend_region_to' => $assigend_region_to,
            'assignment_type_from' => $randomAssignmentType,
            'assignment_type_to' => $randomAssignmentType,
            'public_Type' => $public_Type,
            'main_type_task' => $main_type_taskType,
            'recive_date' => $recive_date,
            'start_date' => $start_date,
            'deadline' => $deadline,
            'hours' => $hours,
            'completion_percentage' => $completion_percentage,
            'recurring' => $recurring,
            'dateTimeCreated' => $dateTimeCreated,
            'recurring_type' => $recurring_type,
            'actual_delivery_date' => $actual_delivery_date,
        ];


        $response = $this->withHeaders([
            'Authorization' => 'Bearer 13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465',
        ])->post('/api/addTask', $requestData);
        info('wwww', array($response->getContent()));
        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', $requestData);
    }

    // public function testChangeStatuseTask()
    // {
    //     $task_statuse = [1, 4, 8, 12];
    //     $task_statuseKey = array_rand($task_statuse);
    //     $task_statuseValue = $task_statuse[$task_statuseKey];
    //     $requestData = [
    //         'task_statuse_id' => $task_statuseValue
    //     ];
    //     $taskId = statuse_task_fraction::inRandomOrder()->first()->id;
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer 13|DuShswbEYoveSyZitaXboyIbl3841qZbuGVNPM7qef237465',
    //     ])->post('/api/changeStatuseTask/' . $taskId, $requestData);

    //     // Decode the response content into an associative array
    //     $responseData = json_decode($response->getContent(), true);

    //     // Check if 'success' property exists and its value is false
    //     if (isset($responseData['success']) && $responseData['success'] === false) {
    //         $response->assertStatus(404);
    //     } else {
    //         $response->assertStatus(200);
    //     }

    //     // Ensure that the data is not present in the database
    //     $this->assertDatabaseMissing('tasks', $requestData);
    // }
}
