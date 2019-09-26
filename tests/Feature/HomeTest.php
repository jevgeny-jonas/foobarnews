<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function testWhenNotLoggedIn()
    {
        $response = $this->get('/home');
        $response->assertStatus(302);
        $response->assertLocation('/login');
    }
    
    public function testWhenLoggedIn()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/home');
        $response->assertStatus(200);
    }
}
