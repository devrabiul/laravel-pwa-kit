<?php

namespace Devrabiul\PwaKit;

use Devrabiul\PwaKit\Commands\PWAUpdateManifestCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

/**
 * Class PwaKitServiceProvider
 *
 * Service provider for the PwaKit Laravel package.
 * Handles:
 * - Bootstrapping PWA routes and assets
 * - Publishing package resources and config
 * - Registering package artisan commands
 * - Setting up singleton bindings for PwaKit
 *
 * @package Devrabiul\PwaKit
 */
class PwaKitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the package services.
     *
     * Called after all other services are registered.
     * - Loads routes
     * - Detects system processing directory
     * - Registers asset service provider
     * - Copies default resources if needed
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        $this->updateProcessingDirectoryConfig();
        $this->app->register(AssetsServiceProvider::class);
        $this->copyResources();
    }

    /**
     * Register package publishable resources.
     *
     * Publishes:
     * - Configuration file to application's config directory
     *
     * Typically used when running artisan vendor:publish
     *
     * @return void
     */
    private function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/config/laravel-pwa-kit.php' => config_path('laravel-pwa-kit.php'),
        ], 'config');
    }

    /**
     * Register artisan commands provided by the package.
     *
     * @return void
     */
    private function registerCommands(): void
    {
        $this->commands([
            PWAUpdateManifestCommand::class,
        ]);
    }

    /**
     * Copy default PWA resources to public and base paths if manifest.json doesn't exist.
     *
     * Copies files:
     * - manifest.json
     * - offline.html
     * - sw.js
     * - logo.png
     *
     * @return void
     */
    private function copyResources(): void
    {
        if (!file_exists(base_path('manifest.json'))) {
            $resources = [
                'manifest.json',
                'offline.html',
                'sw.js',
                'logo.png',
            ];

            $sourcePath = __DIR__ . '/resources';

            foreach ($resources as $file) {
                $sourceFile = $sourcePath . '/' . $file;

                // Copy to public
                $publicFile = public_path($file);
                if (file_exists($sourceFile)) {
                    copy($sourceFile, $publicFile);
                }

                // Copy to base path
                $baseFile = base_path($file);
                copy($sourceFile, $baseFile);
            }
        }
    }

    /**
     * Register package services.
     *
     * - Loads config if missing
     * - Registers singleton for PwaKit
     * - Registers publishable resources in console
     * - Registers artisan commands
     *
     * @return void
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }

        $configPath = config_path('laravel-pwa-kit.php');
        if (!file_exists($configPath)) {
            config(['laravel-pwa-kit' => require __DIR__ . '/config/laravel-pwa-kit.php']);
        }

        $this->registerCommands();

        $this->app->singleton('PwaKit', function ($app) {
            return new PwaKit($app['session'], $app['config']);
        });
    }

    /**
     * Get the services provided by this provider.
     *
     * Used by Laravel's deferred provider mechanism.
     *
     * @return array<string> List of service keys provided.
     */
    public function provides(): array
    {
        return ['PwaKit'];
    }

    /**
     * Determine and set the system processing directory configuration.
     *
     * Sets 'laravel-pwa-kit.system_processing_directory' to:
     * - 'public' if executed from public_path()
     * - 'root' if executed from base_path()
     * - 'unknown' otherwise
     *
     * Useful for adapting asset paths based on execution context.
     *
     * @return void
     */
    private function updateProcessingDirectoryConfig(): void
    {
        $scriptPath = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
        $basePath = realpath(base_path());
        $publicPath = realpath(public_path());

        if ($scriptPath === $publicPath) {
            $systemProcessingDirectory = 'public';
        } elseif ($scriptPath === $basePath) {
            $systemProcessingDirectory = 'root';
        } else {
            $systemProcessingDirectory = 'unknown';
        }

        config(['laravel-pwa-kit.system_processing_directory' => $systemProcessingDirectory]);
    }
}
