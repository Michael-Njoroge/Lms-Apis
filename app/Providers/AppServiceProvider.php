<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        $this->configureRateLimiting();
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });



    VerifyEmail::createUrlUsing(function ($notifiable) {
            $baseUrl = rtrim(env('BASE_URL'), '/');
            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
            $path = parse_url($signedUrl, PHP_URL_PATH);
            $query = parse_url($signedUrl, PHP_URL_QUERY);
            $id = $notifiable->getKey();
            $hash = sha1($notifiable->getEmailForVerification());
            return "{$baseUrl}/email-verification?id={$id}&hash={$hash}&{$query}";
    });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting()
    {
        $this->setRateLimiter('global', 50, 60);
    }

    /**
     * Set rate limiter with custom limit and duration.
     *
     * @param string $name
     * @param int $limit
     * @param int $duration
     */
    protected function setRateLimiter(string $name, int $limit, int $duration)
    {
        RateLimiter::for($name, function (Request $request) use ($limit, $duration) {
            return Limit::perMinutes($duration, $limit)->by(optional($request->user())->id ?: $request->ip())->response(function (Request $request, array $headers) use ($limit, $duration) {
                $retryAfter = $headers['Retry-After'] ?? ($duration * 60);
                return response()->json([
                    'status' => false,
                    'code' => 429,
                    'message' => 'Too many attempts, you are allowed' . $limit . 'requests per' . $duration .'minutes. Please try again after' . $retryAfter . 'seconds.',
                ], 429);
            });
        });
    }
}
