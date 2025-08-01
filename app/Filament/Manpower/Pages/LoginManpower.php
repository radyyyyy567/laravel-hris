<?php
namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class LoginManpower extends BaseLogin
{
    protected static string $view = 'livewire.login-manpower';

    public ?array $data = [
        'email' => '',
        'password' => '',
        'remember' => false,
    ];

    protected $rules = [
        'data.email' => 'required|email',
        'data.password' => 'required|string',
        'data.remember' => 'boolean',
    ];

    protected $messages = [
        'data.email.required' => 'Email address is required.',
        'data.email.email' => 'Please enter a valid email address.',
        'data.password.required' => 'Password is required.',
    ];

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $this->validate();

        if (! auth()->attempt([
            'email' => $this->data['email'],
            'password' => $this->data['password'],
        ], $this->data['remember'])) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}