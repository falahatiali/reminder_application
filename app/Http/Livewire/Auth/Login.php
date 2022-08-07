<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;

class Login extends Component
{
    public $form = [
        'email' => '',
        'password' => '',
    ];

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app');
    }

    public function submit()
    {
        $this->validate([
            'form.email'      => 'required|email',
            'form.password'   => 'required',
        ],[
            'form.email.required' => 'ایمیل ضروری است',
            'form.email.email' => 'فرمت ایمیل اشتباه است',
            'form.password.required' => 'رمز ورود ضروری است',
        ]);

        auth()->attempt($this->form);

        return redirect(route('home'));
    }
}
