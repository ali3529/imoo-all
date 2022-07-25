<?php

namespace App\Providers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            Schema::defaultStringLength(191);
        }

        Response::macro('success', function ($message, $data = '') {
            return response()->json([
                'status'  => 1,
                'message' => $message,
                'data'    => $data,
            ]);
        });

        Response::macro('error', function ($message, $data = '', $code = 400) {
            if (config('app.debug') && $message instanceof Exception) {
                $data = $message->getTrace();
                $message = 'MESSAGE:'.$message->getMessage().
                    ' __ FILE:'.$message->getFile().
                    ' __ DODE:'.$message->getCode().
                    ' __ LINE:'.$message->getLine();
            }
            return response()->json([
                'status' => 0,
                'error'  => config('app.debug') ? $message
                    : trans('messages.error_occurred'),
                'data'   => config('app.debug') ? $data : '',
            ], $code);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')
            && class_exists(DuskServiceProvider::class)
        ) {
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
