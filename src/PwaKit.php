<?php

namespace Devrabiul\PwaKit;

use Devrabiul\PwaKit\Traits\PWATrait;
use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Session\SessionManager as Session;
use Illuminate\Config\Repository as Config;

/**
 * Class PwaKit
 *
 * This class handles Progressive Web App (PWA) integration for a Laravel application.
 * It provides methods to render the required <head> content, scripts for PWA functionality,
 * and manage logo updates.
 */
class PwaKit
{
    use PWATrait;

    /**
     * The session manager instance.
     *
     * @var \Illuminate\Session\SessionManager
     */
    protected $session;

    /**
     * The configuration repository instance.
     *
     * @var Repository
     */
    protected $config;

    /**
     * PwaKit constructor.
     *
     * @param Session $session The session manager instance.
     * @param Config $config The configuration repository instance.
     */
    public function __construct(Session $session, Config $config)
    {
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * Generate the PWA-related <head> HTML.
     *
     * This includes meta tags, manifest link, and CSS for the install toast.
     *
     * @return string The HTML content for the <head>.
     */
    public function head(): string
    {
        return self::generateHead();
    }

    /**
     * Generate the PWA-related scripts for the page.
     *
     * This includes service worker registration and install toast scripts.
     *
     * @return string The HTML <script> tags and inline JS.
     */
    public function scripts(): string
    {
        return self::generateScript();
    }

    /**
     * Handle updating the PWA logo.
     *
     * Validates the uploaded logo, saves it to public and base directories,
     * and returns the paths for usage in the manifest.
     *
     * @param Request $request The HTTP request containing the uploaded logo file.
     * @return array Status, paths, or error messages.
     */
    public function updatePWALogo(Request $request): array
    {
        return self::updatePWALogoFile($request);
    }

    /**
     * Create or update the manifest.json file using provided manifest data.
     *
     * This method delegates the actual writing process to createOrUpdateData(),
     * ensuring the manifest file is generated or updated based on the given
     * configuration.
     *
     * @param array $manifest Associative array representing manifest.json content.
     * @param bool  $force    If true, overwrite existing manifest.json files.
     *
     * @return bool True on success, false on failure.
     */
    public function createOrUpdate(array $manifest, bool $force = false): bool
    {
        return self::createOrUpdateData($manifest, $force);
    }
}
