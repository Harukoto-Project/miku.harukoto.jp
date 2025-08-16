<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // 認証後のリダイレクト先をダッシュボードに設定
        $this->app['config']->set('auth.home', '/dashboard');
        
        // セッションの永続化を確実にする
        $this->app['config']->set('session.secure', false);
        $this->app['config']->set('session.same_site', 'lax');
    }
}
