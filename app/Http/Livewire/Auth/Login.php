<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;

class Login extends Component
{
    public array $form = [
        'email' => '',
        'password' => '',
    ];

    public function render()
    {
        if (auth()->guest()) {
            return view('livewire.auth.login');
        }

        return view('livewire.home')->layout('layouts.app');
    }

    public function submit()
    {
        $this->validate([
            'form.email' => 'required|email',
            'form.password' => 'required',
        ], [
            'form.email.required' => 'ایمیل ضروری است',
            'form.email.email' => 'فرمت ایمیل اشتباه است',
            'form.password.required' => 'رمز ورود ضروری است',
        ]);

        if (auth()->attempt($this->form)) {
            return redirect(route('home'));
        }

        return redirect(route('login'))->with('error', 'failed');

    }
}
