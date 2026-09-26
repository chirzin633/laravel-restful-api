<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityRequirement;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
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
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->components->addSecurityScheme(
                    'basic_auth',
                    SecurityScheme::http('basic')
                );

                $openApi->components->addSecurityScheme(
                    'api_token',
                    SecurityScheme::http('bearer')
                );

                $openApi->security = [
                    new SecurityRequirement(['basic_auth' => []]),
                    new SecurityRequirement(['api_token' => []]),
                ];
            });
    }
}
