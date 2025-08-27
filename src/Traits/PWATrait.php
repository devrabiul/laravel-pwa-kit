<?php

namespace Devrabiul\PwaKit\Traits;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;


trait PWATrait
{

    /**
     * Generate the HTML <head> content for PWA.
     *
     * Includes theme-color meta, apple-touch-icon, manifest link, and PWA CSS.
     *
     * @return string
     */
    public static function generateHead(): string
    {
        $manifestConfig = config('laravel-pwa-kit.manifest', []);
        $themeColor = $manifestConfig['theme_color'] ?? $manifestConfig['themeColor'] ?? '#6777ef';
        $icon = $manifestConfig['icons'][0]['src'] ?? asset('/logo.png');
        $manifestUrl = route('laravel-pwa-kit.manifest-json');

        $icon = $manifestConfig['icons'][0]['src'] ?? asset('logo.png');
        if (!str_starts_with($icon, 'http')) {
            $icon = asset($icon);
        }

        $defaultJsPath = 'packages/devrabiul/laravel-pwa-kit/css/laravel-pwa-kit.css';
        if (File::exists(public_path($defaultJsPath))) {
            $styleSrc = self::getDynamicAsset($defaultJsPath);
        } else {
            $styleSrc = self::getDynamicAsset('vendor/devrabiul/laravel-pwa-kit/assets/css/laravel-pwa-kit.css');
        }

        return <<<HTML
        <!-- PWA -->
        <meta name="theme-color" content="{$themeColor}" />
        <link rel="apple-touch-icon" href="{$icon}" />
        <link rel="manifest" href="{$manifestUrl}" />
        <link rel="stylesheet" href="{$styleSrc}">
        <!-- PWA end -->
        HTML;
    }

    /**
     * Generate the PWA-related scripts for the page.
     *
     * Handles service worker registration, toast installation prompt, and script inclusion.
     *
     * @return string
     */
    public static function generateScript(): string
    {
        $enablePWA = config('laravel-pwa-kit.enable_pwa', false);
        $script = '';

        if ($enablePWA) {
            $isDebug = config('laravel-pwa-kit.debug', false);
            $consoleLog = 'console.log';

            $manifestConfig = config('laravel-pwa-kit.manifest', []);
            $icon = '';

            $swPath = self::getDynamicAsset('sw.js');
            $isLivewire = config('laravel-pwa-kit.livewire-app', false) ? 'data-navigate-once' : '';

            // Start building the script
            $script .= self::getInstallAppHtml($enablePWA, $icon);
            $script .= self::scriptsPath();
            $script .= '<script ' . $isLivewire . ' src="' . $swPath . '"></script>';
            $script .= '<script ' . $isLivewire . '>';
            $script .= '"use strict";';
            $script .= 'document.addEventListener("DOMContentLoaded", function() {';
            $script .= 'if ("serviceWorker" in navigator) {';
            $script .= 'navigator.serviceWorker.register("' . $swPath . '").then(';
            $script .= 'function(registration) { ' . ($isDebug ? $consoleLog . '("Service worker registration succeeded:", registration);' : '') . ' },';
            $script .= 'function(error) { ' . ($isDebug ? $consoleLog . '("Service worker registration failed:", error);' : '') . ' }';
            $script .= ');';
            $script .= '} else { ' . ($isDebug ? $consoleLog . '("Service workers are not supported.");' : '') . ' }';

            // Show toast on first load
            if (config('laravel-pwa-kit.install-toast-show', false)) {
                $script .= 'if(!window.matchMedia("(display-mode: standalone)").matches && !isToastShown()){';
                $script .= 'setTimeout(()=>{showInstallPromotion(); localStorage.setItem("pwaToastShown",Date.now());},3000);}';
            }

            $script .= '});';
            $script .= '</script>';
        }

        return $script;
    }

    /**
     * Generate HTML <script> tags for the PWA JS file.
     *
     * @return string
     */
    public static function scriptsPath(): string
    {
        $defaultJsPath = 'packages/devrabiul/laravel-pwa-kit/js/laravel-pwa-kit.js';
        if (File::exists(public_path($defaultJsPath))) {
            return self::scriptTag($defaultJsPath);
        }
        return self::scriptTag('vendor/devrabiul/laravel-pwa-kit/assets/js/laravel-pwa-kit.js');
    }

    /**
     * Generate a <script> tag for a given asset path.
     *
     * @param string $src
     * @return string
     */
    public static function scriptTag(string $src): string
    {
        return '<script src="' . self::getDynamicAsset($src) . '"></script>';
    }

    /**
     * Return a full URL for a given asset path, handling different directories.
     *
     * @param string $path
     * @return string
     */
    public static function getDynamicAsset(string $path): string
    {
        if (config('laravel-pwa-kit.system_processing_directory') == 'public') {
            $position = strpos($path, 'public/');
            $result = $path;
            if ($position === 0) {
                $result = preg_replace('/public/', '', $path, 1);
            }
        } else if (
            (str_contains(realpath(public_path()), 'public\public') ||
                str_contains(realpath(public_path()), 'public/public')) &&
            PHP_OS_FAMILY === 'Windows'
        ) {
            $result = 'public/' . $path;
        } else {
            $result = in_array(request()->ip(), ['127.0.0.1']) ? $path : 'public/' . $path;
        }

        return asset($result);
    }

    /**
     * Generate the HTML for the install app toast.
     *
     * @param bool $enablePWA
     * @param string $icon
     * @return string
     */
    private static function getInstallAppHtml(bool $enablePWA, string $icon): string
    {
        if (!$enablePWA) {
            return '';
        }

        $config = config('laravel-pwa-kit', []);

        $title = $config['title'] ?? 'Welcome to ' . e(config('app.name')) . '!';
        $description = $config['description']
            ?? 'Click the <strong>Install Now</strong> button & enjoy it just like an app.';
        $appName = config('app.name', 'Laravel');
        $buttonText = $config['install_now_button_text'] ?? 'Install Now';
        $smallDevicePosition = in_array($config['small_device_position'], ['top', 'bottom']) ? 'small-device-' . $config['small_device_position'] : '';

        return <<<HTML
            <div class="app-install-toast {$smallDevicePosition}" role="alert" id="install-prompt" aria-label="Install {$appName}">
                <div class="app-install-toast-content">
                    <div class="app-install-toast-text">
                        <h6 class="app-install-toast-title">{$title}</h6>
                        <p class="app-install-toast-desc">{$description}</p>
                        <button id="installPWAButton" class="app-install-toast-action">
                            {$buttonText}
                        </button>
                    </div>
                </div>
                <button
                    class="app-install-toast-btn-close"
                    type="button"
                    id="install-pwa-button-close"
                    aria-label="Close install prompt">
                </button>
            </div>
        HTML;
    }

    /**
     * Create or update the manifest.json file.
     *
     * @param array $manifest
     * @param bool $force Overwrite if file exists
     * @return bool
     */
    public function createOrUpdateData(array $manifest, bool $force = false): bool
    {
        // Ensure defaults
        $startUrl = $manifest['start_url'] ?? '/';
        $icons = $manifest['icons'] ?? [];

        // Clean + rebuild
        unset($manifest['start_url'], $manifest['icons']);
        $finalManifest = array_merge($manifest, [
            'start_url' => $startUrl,
            'icons' => $icons,
        ]);

        // Encode JSON
        $jsonData = json_encode($finalManifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($jsonData === false) {
            return false;
        }

        // Paths (both public and base)
        $targets = [
            public_path('manifest.json'),
            base_path('manifest.json'),
        ];

        foreach ($targets as $filePath) {
            $dir = dirname($filePath);

            if (!is_writable($dir)) {
                return false;
            }

            if (file_exists($filePath) && !$force) {
                continue; // skip this one, try next
            }

            try {
                // Atomic write
                $tmpPath = $filePath . '.tmp';
                file_put_contents($tmpPath, $jsonData);
                rename($tmpPath, $filePath);
            } catch (\Throwable $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update the manifest.json file.
     *
     * @param array $manifestData
     * @return bool
     */
    public function update(array $manifestData): bool
    {
        try {
            if ($this->createOrUpdate($manifestData)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Update the PWA logo file.
     *
     * This method handles a logo upload request, validates the uploaded file,
     * deletes any existing logo in both public and base paths, saves the new file,
     * and returns the public URL and base path for reference.
     *
     * Validation:
     * - Must be a PNG image
     * - Minimum dimensions: 512x512 pixels
     * - Maximum file size: 2MB
     *
     * @param object|array $request The Laravel request object containing the uploaded 'logo' file.
     *
     * @return array An associative array containing the result:
     *   - 'status' => bool  Indicates success or failure.
     *   - 'message' => string  Success message (if successful).
     *   - 'paths' => array  Array with 'public' and 'base' paths to the logo file.
     *   - 'errors' => array  Validation errors (if validation fails).
     *   - 'error' => string  General error message (if exception occurs).
     *   - 'debug' => string  Optional debug information (exception message).
     *
     * @throws ValidationException If validation fails and is not caught.
     * @throws Exception For other unexpected errors during file operations.
     */
    public static function updatePWALogoFile(object|array $request): array
    {
        try {
            // Validate uploaded logo
            $request->validate([
                'logo' => 'required|image|mimes:png|dimensions:min_width=512,min_height=512|max:2048',
            ]);

            $file = $request->file('logo');
            $fileName = 'logo.png';

            // Paths
            $publicPath = public_path($fileName);
            $basePath = base_path($fileName);

            // Delete old files if exist
            foreach ([$publicPath, $basePath] as $path) {
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            // Save to public_path
            $file->move(public_path(), $fileName);

            // Also copy to base_path
            File::copy(public_path($fileName), $basePath);

            return [
                'status' => true,
                'message' => 'Logo updated successfully!',
                'paths' => [
                    'public' => asset($fileName),
                    'base' => $basePath,
                ],
            ];

        } catch (ValidationException $e) {
            return [
                'status' => false,
                'errors' => $e->errors(),
            ];

        } catch (Exception $e) {
            return [
                'status' => false,
                'error' => 'Something went wrong. Please try again.',
                'debug' => $e->getMessage(), // optional: helpful for debugging
            ];
        }
    }
}
