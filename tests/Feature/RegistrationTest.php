<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Str;

class RegistrationTest extends TestCase
{
    public function testRegistrationFormDisplayed()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function validDataProvider()
    {
        $email1 = Str::random(12) . '@tEst';
        $email2 = Str::random(12) . '@tEst';
        
        return [
            'basic' => [
                'email_register' => $email1,
                'email_login' => $email1,
            ],
            'email_in_different_case_with_spaces_and_tabs' => [
                'email_register' => " \t " . strtolower($email2) . " \t ",
                'email_login' => $email2,
            ],
        ];
    }
    
    /**
     * @dataProvider validDataProvider
     */
    public function testWithValidData(string $emailRegister, string $emailLogin)
    {
        $response = $this->post('/register', [
            'email' => $emailRegister,
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        
        $this->post('/logout');
        $this->assertGuest();
        $this->post('/login', [
            'email' => $emailLogin,
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function invalidDataProvider()
    {
        return [
            'too_short_password' => [
                'data' => [
                    'email' => Str::random(12) . '@test.test',
                    'password' => 'abcdefg',
                ],
            ],
            'too_long_email' => [
                'data' => [
                    'email' => Str::random(255) . '@test.test',
                    'password' => 'password',
                ],
            ],
            'too_short_password_and_too_long_email' => [
                'data' => [
                    'email' => Str::random(255) . '@test.test',
                    'password' => 'abcdefg',
                ],
            ],
        ];
    }
    
    /**
     * @dataProvider invalidDataProvider
     */
    public function testWithInvalidData(array $data)
    {
        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
    
    public function testWithExistingEmail()
    {
        $user = factory(User::class)->create();
        
        $response = $this->post('/register', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
        
        return $user;
    }
    
    /**
     * @depends testWithExistingEmail
     */
    public function testWithExistingEmailInDifferentCase(User $user)
    {
        $response = $this->post('/register', [
            'email' => strtoupper($user->email),
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
    
    /**
     * @depends testWithExistingEmail
     */
    public function testWithExistingEmailWithSpacesAndTabsAtStart(User $user)
    {
        $response = $this->post('/register', [
            'email' => " \t " . $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
    
    /**
     * @depends testWithExistingEmail
     */
    public function testWithExistingEmailWithSpacesAndTabsAtEnd(User $user)
    {
        $response = $this->post('/register', [
            'email' => $user->email . " \t ",
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
