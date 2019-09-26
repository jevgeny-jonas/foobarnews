<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Str;

class LoginLogoutTest extends TestCase
{
    public function testLoginFormDisplayed()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function testLoginWithValidCredentials()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
        
        return $user;
    }

    /**
     * @depends testLoginWithValidCredentials
     */
    public function testLoginWithValidCredentialsWithEmailInDifferentCaseWithSpacesAndTabs(User $user)
    {
        $response = $this->post('/login', [
            'email' => " \t " . strtoupper($user->email) . " \t ",
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }
    
    public function testLoginWithInvalidPassword()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'invalid',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function testLoginWithNonexistentEmail()
    {
        $response = $this->post('/login', [
            'email' => Str::random(35) . '@test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
    
    public function testRememberMeFunctionality()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => 'on',
        ]);
        
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertCookie(\Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        $this->assertAuthenticatedAs($user);
    }
    
    public function testLoginAlreadyAuthenticatedUser()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }
    
    public function testLogoutAuthenticatedUser()
    {
        $user = factory(User::class)->make();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertStatus(302);
        $this->assertGuest();
    }

    public function testLogoutAlreadyLoggedOutUser()
    {
        $response = $this->post('/logout');
        
        $response->assertStatus(302);
        $this->assertGuest();
    }
}
