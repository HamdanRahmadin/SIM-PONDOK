<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('SIM-PONDOK Login')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function mount()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            return redirect()->to("/{$role}/dashboard");
        }
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            $role = Auth::user()->role;
            return redirect()->to("/{$role}/dashboard");
        }

        $this->addError('email', 'Kredensial yang diberikan tidak cocok dengan data kami.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.blank');
    }
}
