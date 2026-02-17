<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::loginView(fn () => view('auth.login'));

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            if ($user->role === User::ROLE_EMPLOYEE && $user->pressing) {
                $closing = $user->pressing->closing_time;

                if ($closing) {
                    $cutoff = now()->setTimeFromTimeString($closing)->addMinutes(30);

                    if (now()->greaterThan($cutoff)) {
                        $owner = User::where('pressing_id', $user->pressing_id)->where('role', User::ROLE_OWNER)->first();
                        if ($owner) {
                            UserNotification::create([
                                'user_id' => $owner->id,
                                'type' => 'late_login_attempt',
                                'title' => 'Tentative de connexion hors horaires',
                                'message' => $user->name.' a tenté de se connecter à '.now()->format('H:i').'.',
                                'data' => ['employee_id' => $user->id],
                            ]);
                        }

                        return null;
                    }
                }
            }

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });
    }
}
