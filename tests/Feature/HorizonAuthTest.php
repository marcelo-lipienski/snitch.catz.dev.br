<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HorizonAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_horizon_is_accessible_in_local_environment()
    {
        $this->app['env'] = 'local';
        
        $response = $this->get('/horizon');

        $response->assertStatus(200);
        $response->assertSee('Horizon');
    }

    public function test_horizon_is_protected_by_basic_auth_in_production()
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);
        
        // Mock env variables for the middleware
        putenv('HORIZON_USER=admin');
        putenv('HORIZON_PASSWORD=password');

        $response = $this->get('/horizon');

        $response->assertStatus(401);
        $response->assertHeader('WWW-Authenticate');
    }

    public function test_horizon_is_accessible_with_correct_credentials()
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);
        
        putenv('HORIZON_USER=admin');
        putenv('HORIZON_PASSWORD=password');

        $response = $this->get('/horizon', [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Horizon');
    }

    public function test_horizon_fails_with_incorrect_credentials()
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);
        
        putenv('HORIZON_USER=admin');
        putenv('HORIZON_PASSWORD=password');

        $response = $this->get('/horizon', [
            'PHP_AUTH_USER' => 'wrong',
            'PHP_AUTH_PW' => 'password',
        ]);

        $response->assertStatus(401);
    }
}
