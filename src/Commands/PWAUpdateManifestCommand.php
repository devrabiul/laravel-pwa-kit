<?php

namespace Devrabiul\PwaKit\Commands;

use Devrabiul\PwaKit\Traits\PWATrait;
use Illuminate\Console\Command;

class PWAUpdateManifestCommand extends Command
{
    use PWATrait;

    protected $signature = 'pwa:update-manifest {--force : Overwrite existing manifest.json without confirmation}';

    protected $description = 'Generate or update the manifest.json file for the PWA.';

    public function handle(): int
    {
        try {
            $manifest = config('laravel-pwa-kit.manifest', []);

            if (empty($manifest)) {
                $this->error('❌ Manifest configuration is empty. Please check config/laravel-pwa-kit.php');
                return self::FAILURE;
            }

            if (empty($manifest['icons'])) {
                $this->error('⚠️ Manifest is missing required "icons". Operation aborted.');
                return self::FAILURE;
            }

            $this->line('🔄 Updating manifest.json...');

            $updated = $this->createOrUpdate($manifest, $this->option('force'));

            if ($updated) {
                $this->info('✅ Manifest JSON updated successfully at public/manifest.json');
                return self::SUCCESS;
            }

            $this->warn('⚠️ Manifest file was not updated.');
            return self::FAILURE;

        } catch (\Throwable $e) {
            $this->error('❌ Error while updating the manifest: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
