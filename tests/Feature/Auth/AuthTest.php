<?php

namespace Tests\Feature;

use App\Models\users;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;
    public function testCheckEmail()
    {
        $email = $this->faker->safeEmail;
        $requestData = [
            'email' => $email
        ];
        $response = $this->withHeaders([])->post('/api/checkEmail', $requestData);
        $responseData = $response->json();

        $userExists = users::where('email', $email)->exists();
        if ($userExists) {
            $this->assertTrue($userExists);
            $response->assertStatus(200);
            $this->assertTrue($responseData['success']);
        } else {
            $this->assertFalse($userExists);
            $response->assertStatus(401);
            $this->assertFalse($responseData['success']);
        }

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);
    }

    public function testLogin()
    {
        $email = $this->faker->safeEmail;
        $otp = $this->faker->randomNumber(5, true);
        $requestData = [
            'email' => $email,
            'otp' => $otp
        ];

        $response = $this->withHeaders([])->post('/api/login', $requestData);
        $responseData = $response->json();

        $userExists = users::where('email', $email)->exists();
        $otpEsists = users::where('code_verfiy', $otp)->exists();
        if ($userExists && $otpEsists) {
            $this->assertTrue($userExists);
            $this->assertTrue($otpEsists);
            $response->assertStatus(200);
            $this->assertTrue($responseData['success']);
        } else {
            $this->assertFalse($userExists);
            $this->assertFalse($otpEsists);
            $response->assertStatus(401);
            $this->assertFalse($responseData['success']);
        }

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);
    }


    public function testCheckActualEmail()
    {
        $requestData = [
            'email' => 'sabo51051@gmail.com',
        ];
        $response = $this->withHeaders([])->post('/api/checkEmail', $requestData);
        $responseData = $response->json();
        $email = 'sabo51051@gmail.com';
        $userExists = users::where('email', $email)->exists();

        $this->assertTrue($userExists);
        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);


        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);
    }

    public function testAuctualLogin()
    {
        $actualEmail = 'sabo51051@gmail.com';
        $otpActual = users::where('email', $actualEmail)->first()->code_verfiy;
        $requestData = [
            'email' => $actualEmail,
            'otp' => $otpActual
        ];

        $response = $this->withHeaders([])->post('/api/login', $requestData);
        $responseData = $response->json();

        $userExists = users::where('email', $actualEmail)->exists();
        $otpEsists = users::where('code_verfiy', $otpActual)->exists();

        $this->assertTrue($userExists);
        $this->assertTrue($otpEsists);
        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);
    }

    public function testPhoneAppLogin()
    {
        $PhoneAppEmail = 'smartlife2@gmail.com';
        $requestData = [
            'email' => $PhoneAppEmail,
            'otp' => 902461
        ];

        $response = $this->withHeaders([])->post('/api/login', $requestData);
        $responseData = $response->json();

        $userExists = users::where('email', $PhoneAppEmail)->exists();
        $otpEsists = users::where('code_verfiy', 902461)->exists();

        $this->assertTrue($userExists);
        $this->assertTrue($otpEsists);
        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);

        $result = $response->decodeResponseJson()['data'];
        $this->assertEquals($result, $responseData['data']);
    }



}
