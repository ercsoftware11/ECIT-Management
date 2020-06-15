<?php

namespace Tests\Unit;

use App\Business;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessTest extends TestCase
{

    // setup and tear down
    use RefreshDatabase;

    public function test_a_business_can_be_created()
    {
        // admin
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->admin())->post('/business', $this->data());

        $this->assertCount(1, Business::all());

        // user
        $response = $this->actingAs($this->user())->post('/business', array_merge($this->data(), ['name' => 'Test Company 2']));

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_a_business_can_be_updated()
    {
        $this->withoutExceptionHandling();

        // admin
        $this->actingAs($this->admin())->post('/business', $this->data());

        $business = Business::first();

        $response = $this->actingAs($this->admin())->patch($business->path(), array_merge($this->data(), ['name' => 'Test Updated']));

        $this->assertEquals('Test Updated', Business::first()->name);

        // user
        $business = Business::first();

        $response = $this->actingAs($this->user())->patch($business->path(), array_merge($this->data(), ['name' => 'Test Updated Guest']));

        $business = Business::first();

        $this->assertEquals('Test Updated', $business->name);
        $response->assertStatus(302);
    }

    public function test_user_updating_is_allowed()
    {
        $user = $this->user(1);

        $this->actingAs($this->admin())->post('/business', $this->data());

        $business = Business::first();

        $response = $this->actingAs($user)->patch($business->path(), array_merge($this->data(), ['address' => '1 Test Street']));

        $business = Business::first();

        $this->assertEquals('1 Test Street', $business->address);
    }

    public function test_a_business_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        // admin
        $this->actingAs($this->admin())->post('/business', $this->data());

        $business = Business::first();
        $this->assertCount(1, Business::all());

        $response = $this->actingAs($this->admin())->delete($business->path());

        $this->count(0, Business::all());

        // user
        $this->actingAs($this->admin())->post('/business', $this->data());

        $business = Business::first();
        $this->assertCount(1, Business::all());

        $response = $this->actingAs($this->user())->delete($business->path());

        $this->count(1, Business::all());
    }

    private function data()
    {
        return [
            'id' => '1',
            'name' => 'Test Company',
            'address' => '',
            'abn' => '',
            'phone' => '',
            'email' => '',
            'web' => '',
            'primary_contact_id' => '',
        ];
    }

    private function admin()
    {
        $result = factory(User::class)->create();
        $result->role = 'admin';
        $result->save();
        return $result;
    }

    private function user($business_id=null)
    {
        $result = factory(User::class)->create();
        if(!$business_id == null) {
            $result->business_id = $business_id;
        }
        $result->save();
        return $result;
    }
}
