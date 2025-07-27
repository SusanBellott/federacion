<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::authenticateUsing(function (Request $request) {
//codigo capcha
 $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
       'captcha' => 'required|string|min:6|max:6',

    ], [
        'email.required' => 'El correo es obligatorio.',
        'password.required' => 'La contraseña es obligatoria.',
      'captcha.required' => 'El código captcha es obligatorio.',
    ]);

    if ($validator->fails()) {
        throw ValidationException::withMessages($validator->errors()->toArray());
    }

    // Validar Captcha comparando con lo guardado en la sesión
    if (
        !session()->has('captcha_code') ||
        strtoupper($request->input('captcha')) !== session('captcha_code')
    ) {
        throw ValidationException::withMessages([
            'captcha' => 'El código captcha ingresado es incorrecto.',
        ]);
    }


            // Buscar el usuario ignorando mayúsculas/minúsculas
            $user = User::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();
        
            if ($user && $user->estado === 'activo' && Hash::check($request->password, $user->password)) {
                return $user;
            }
        
            if ($user && $user->estado === 'inactivo') {
                throw ValidationException::withMessages([
                    'email' => 'Su cuenta ha sido bloqueada. Contáctese con la administración.',
                ]);
            }
        
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        });
        
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(500)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(500)->by($request->session()->get('login.id'));
        });
    }
}
