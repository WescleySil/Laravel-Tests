<?php

use App\Models\User;

use function \Pest\Laravel\postJson;

it('Should auth user', function () {
    $user = \App\Models\User::factory()->create();
    $data  = [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'e2e_test'
    ];
    postJson(route('auth.login'),$data)
        ->assertOk()
        ->assertJsonStructure(['token']);
});

test('should fail auth', function (){
   $user = User::factory()->create();
   postJson(route('auth.login'), [
      'email' => $user->email,
      'password' => 'testing',
      'device_name' => 'e2e_test'
   ])
   ->assertStatus(422);
});

describe('validation', function (){
    it('should require email', function (){
       postJson(route('auth.login'),[
           'password' => 'teste',
           'device_name' => 'e2e_test'
       ])
        ->assertJsonValidationErrors([
            'email' => trans('validation.required',['attribute' =>'email'])
        ])
        ->assertStatus(422);
    });
    it('should require password', function (){
        $user  = User::factory()->create();
        postJson(route('auth.login'),[
            'email' => $user->email,
            'password' => '',
            'device_name' => 'e2e_test'
        ])
            ->assertJsonValidationErrors([
                'password' => trans('validation.required',['attribute' =>'password'])
            ])
            ->assertStatus(422);
    });
    it('should require device name', function (){
        $user  = User::factory()->create();
        postJson(route('auth.login'),[
            'email' => $user->email,
            'password' => 'password',
            'device_name' => ''
        ])
            ->assertJsonValidationErrors([
                'device_name' => trans('validation.required',['attribute' =>'device name'])
            ])
            ->assertStatus(422);
    });
});
