<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;

class Register extends Component
{
    public array $form = [
        'name' => '',
        'username' => '',
        'email' => '',
        'password' => '',
    ];

    public function render()
    {
        if (!auth()->check()) {
            return view('livewire.auth.register')
                ->layout('layouts.app');
        }
        return view('livewire.home')->layout('layouts.app');
    }

    public function submit()
    {
        $this->validate([
            'form.email' => 'required|email',
            'form.name' => 'required',
            'form.username' => 'required',
            'form.password' => 'required',
        ], [
            'form.email.required' => 'ایمیل ضروری است',
            'form.email.email' => 'فرمت ایمیل اشتباه است',
            'form.name.required' => 'نام ضروری است',
            'form.username.required' => 'نام کاربری ضروری است',
            'form.password.required' => 'رمز ورود ضروری است',
        ]);

        $user = User::create($this->form);

        auth()->login($user);
        return redirect(route('home'));
    }
}
