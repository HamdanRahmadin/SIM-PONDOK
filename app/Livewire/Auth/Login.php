<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("RIBATHUL QUR'AN Login")]
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

        $executed = RateLimiter::attempt(
            'login:'.request()->ip(),
            5,
            function () {
                if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                    session()->regenerate();
                    $role = Auth::user()->role;

                    return redirect()->to("/{$role}/dashboard");
                }

                $this->addError('email', 'Kredensial yang diberikan tidak cocok dengan data kami.');
            }
        );

        if (! $executed) {
            $seconds = RateLimiter::availableIn('login:'.request()->ip());
            $this->addError('email', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.blank');
    }
}
