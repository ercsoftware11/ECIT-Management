<?php

namespace Tests\Unit;

use App\Business;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{

    // setup and tear down
    use RefreshDatabase;

    public function test_a_user_can_be_created()
    {
        $this->withoutExceptionHandling();

        // admin
        $admin = $this->admin();

        $this->actingAs($admin)->post('/business', $this->business_data());
        $this->actingAs($admin)->post('/user', $this->user_data());

        $this->assertCount(2, User::all());

        $user = User::where('id', '=',  2)->first();

        $this->assertEquals('Test Name', $user->name);
        $this->assertEquals('user', $user->role);
        $this->assertEquals('Test Company', $user->business->name);

        // user
        $user = $this->user();

        $response = $this->actingAs($user)->post('/user', array_merge($this->user_data(), ['name' => 'Test User']));

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_a_user_can_be_updated()
    {
        $this->withoutExceptionHandling();

        // admin
        $admin = $this->admin();

        $this->actingAs($admin)->post('/user', $this->user_data());

        $this->actingAs($admin)->patch('/user', array_merge($this->user_data(), ['id' => '2', 'name' => 'Updated Name']));

        $updated_user = User::where('id', '=', 2)->first();

        $this->assertEquals('Updated Name', $updated_user->name);

        // client
        $user = $this->user();

        $this->actingAs($user)->patch('/user', array_merge($this->user_data(), ['id' => '2', 'name' => 'User Name']));

        $updated_user = User::where('id', '=', 2)->first();

        $this->assertEquals('User Name', $updated_user->name);
    }

    public function test_update_fail()
    {
        $this->withoutExceptionHandling();

        // admin
        $admin = $this->admin();

        $this->actingAs($admin)->post('/user', $this->user_data());

        // client
        $user = $this->user();

        $response = $this->actingAs($user)->patch('/user', array_merge($this->user_data(), ['name' => 'User Name Updated']));

        $updated_user = User::where('id', '=', 2)->first();

        $this->assertEquals('Test Name', $updated_user->name);

        $response->assertStatus(302);
    }

    public function test_admin_can_update_admin_fields_of_any_user()
    {
        $this->withoutExceptionHandling();

        // admin
        $admin = $this->admin();

        $this->actingAs($admin)->post('/user', $this->user_data());

        $response = $this->actingAs($admin)->patch('/user', array_merge($this->user_data(), ['id' => 2, 'name' => 'Hello', 'business_id' => 5]));

        $user = User::where('id', '=', 2)->first();

        $this->assertEquals(2, $user->id);
        $this->assertEquals('Hello', $user->name);
        $this->assertEquals(5, $user->business_id);
    }

    public function test_a_user_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        // admin
        $admin = $this->admin();

        $this->actingAs($admin)->post('/user', $this->user_data());

        $user_to_delete = User::where('id', '=', 2)->first();

        $this->actingAs($admin)->delete($user_to_delete->path());

        $this->assertCount(1, User::all());

        // user
        $user = $this->user();

        $this->actingAs($admin)->post('/user', array_merge($this->user_data(), ['name' => 'Test Example']));

        $user_to_delete = User::where('id', '=', 4)->first();

        $response = $this->actingAs($user)->delete($user_to_delete->path());

        $response->assertStatus(302);

        $this->assertEquals('Test Example', User::where('id', '=', 4)->first()->name);
    }

    private function user_data()
    {
        return [
            'id' => '',
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'password' => 'password',
            'phone' => '0411111111',
            'business_id' => '1',
        ];
    }

    private function business_data()
    {
        return [
            'id' => '1',
            'name' => 'Test Company',
            'address' => '',
            'abn' => '',
            'phone' => '',
            'email' => '',
            'web' => '',
            'primary_contact' => '2',
        ];
    }

    private function admin()
    {
        $result = factory(User::class)->create();
        $result->role = 'admin';
        $result->save();
        return $result;
    }

    private function user()
    {
        $result = factory(User::class)->create();
        $result->save();
        return $result;
    }
}
