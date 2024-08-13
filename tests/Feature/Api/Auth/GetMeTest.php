<?php

use \App\Models\User;
use \App\Models\Permission;

use function \Pest\Laravel\getJson;
use function \Pest\Laravel\postJson;


test('unauthenticated user cannot get data', function (){
    getJson(route('auth.me'),[])
    ->assertJson([
        'message' => 'Unauthenticated.'
    ])
    ->assertStatus(401);
});

test('should return user with our data', function (){
    $user = User::factory()->create();
    $token = $user->createToken('test_e2e')->plainTextToken;
    getJson(route('auth.me'),[
        'Authorization' => "Bearer ${token}"
    ])
    ->assertJsonStructure([
        'data' => [
            'id',
            'name',
            'email' ,
            'permissions' => []
        ]
    ])
    ->assertOK();

});

test('should return user with our data and our permissions', function (){
    Permission::factory()->count(10)->create();
    $user = User::factory()->create();
    $permissions = Permission::factory()->count(10)->create()->pluck('id')->toArray();
    $token = $user->createToken('test_e2e')->plainTextToken;
    $user->permissions()->attach($permissions);
    getJson(route('auth.me'),[
        'Authorization' => "Bearer ${token}"
    ])
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email' ,
                'permissions' => [
                    '*' => [
                        'id',
                        'name',
                        'description'
                    ]
                ]
            ]
        ])
        ->assertJsonCount(10,'data.permissions')
        ->assertOK();

});
