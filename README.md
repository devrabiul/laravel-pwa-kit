# Laravel PWA Kit – Laravel to PWA in Minutes

**Laravel PWA Kit** is a powerful, easy-to-use Laravel package that transforms your web applications into **Progressive Web Apps (PWAs)**. With this package, your Laravel apps can be **installable, offline-ready, fast, and engaging**, without writing complex service worker logic manually.

[![Latest Stable Version](https://poser.pugx.org/devrabiul/laravel-pwa-kit/v/stable)](https://packagist.org/packages/devrabiul/laravel-pwa-kit)
[![Total Downloads](https://poser.pugx.org/devrabiul/laravel-pwa-kit/downloads)](https://packagist.org/packages/devrabiul/laravel-pwa-kit)
![GitHub license](https://img.shields.io/github/license/devrabiul/laravel-pwa-kit)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/devrabiul/laravel-pwa-kit)

---

## ✨ Features

* ⚙️ **Automatic Manifest & Service Worker Generation** – No manual setup needed.
* 📲 **Add-to-Home-Screen Install Prompt** – Fully configurable toast notification.
* 🖥️📱 **Responsive & Cross-Platform** – Works on mobile, tablet, and desktop.
* 🔄 **Laravel 8.x → 12.x Compatible** – Supports latest Laravel versions.
* 🛠️ **Customizable** – Modify icons, theme colors, app name, shortcuts via config.
* ⚡ **Offline Ready** – Supports offline pages and caching strategies.
* 🔐 **HTTPS Ready** – Fully compatible with HTTPS-secured applications.
* 🧩 **Livewire & SPA Friendly** – Works out-of-the-box with Livewire v3, Vue 3, and React.
* 🌱 **Treeware Package** – Support environmental initiatives by contributing to tree planting.

---

## 🖼️ Screenshots / Demo

See **Laravel PWA Kit** in action:

[![install-toast.png](https://i.postimg.cc/XvmLfpjJ/install-toast.png)](https://postimg.cc/VS9X1sbQ)
[![offline-page.png](https://i.postimg.cc/zBhj08vk/offline-page.png)](https://postimg.cc/KkZBYXnK)
[![installed-pwa.png](https://i.postimg.cc/vTdvKk6w/installed-pwa.png)](https://postimg.cc/dk4dkWB5)

**Descriptions:**

* **Install Toast Prompt** – The prompt displayed when users can add your app to the home screen.
* **Offline Page** – Shown when the user is offline.
* **Installed PWA** – Your Laravel app running as an installable Progressive Web App.

> **Live Demo:** [https://packages.rixetbd.com/laravel-pwa-kit](https://packages.rixetbd.com/laravel-pwa-kit)

---
## ⚠️ Important

PWAs require **HTTPS** to work correctly. Make sure your application is hosted with HTTPS; otherwise, service workers and other PWA features will not function properly.

> **Note:** For local development, you **can** use `php artisan serve`. Browsers allow service workers on `localhost` over HTTP, so you can test your PWA without HTTPS during development.

---
## 📦 Installation

Install via Composer:

```bash
composer require devrabiul/laravel-pwa-kit
```

Publish configuration and assets:

```bash
php artisan vendor:publish --provider="Devrabiul\PwaKit\PwaKitServiceProvider"
```

This publishes:

* `config/laravel-pwa-kit.php`
* `manifest.json`, `sw.js`, `offline.html`, and `logo.png` to both **public** and **base** directories.

---

## ⚙️ Configuration

Edit `config/laravel-pwa-kit.php` to customize your PWA:

```php
return [
    'enable_pwa' => true,
    'install-toast-show' => true,
    'manifest' => [
        'name' => env('APP_NAME', 'Laravel'),
        'short_name' => 'LPT',
        'start_url' => '/',
        'theme_color' => '#FF5733',
        'background_color' => '#ffffff',
        'display' => 'standalone',
        'icons' => [
            [
                'src' => 'logo.png',
                'sizes' => '512x512',
                'type' => 'image/png',
            ]
        ],
    ],
    'livewire-app' => false,
];
```

---

## 🖥️ Usage

### Include Assets in Blade

**In `<head>`:**

```blade
{!! PwaKit::head() !!}
```

**Before `</body>`:**

```blade
{!! PwaKit::scripts() !!}
```

**Example Layout:**

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My PWA App</title>

    {!! PwaKit::head() !!}
</head>
<body>
    <h1>Welcome to My PWA App</h1>

    {!! PwaKit::scripts() !!}
</body>
</html>
```

---

### Livewire Support

Enable in `config/laravel-pwa-kit.php`:

```php
'livewire-app' => true,
```

This ensures your service worker and toast behave correctly in **Livewire SPA apps**.


---

## 🛠️ Major Methods & Usage

| Method                                                 | Description                                                        | Example                                                 |
| ------------------------------------------------------ | ------------------------------------------------------------------ | ------------------------------------------------------- |
| `PwaKit::head()`                                       | Generates `<meta>`, `<link>` and stylesheet tags for PWA.          | `{!! PwaKit::head() !!}`                                |
| `PwaKit::scripts()`                                    | Registers service worker, scripts, and install toast HTML.         | `{!! PwaKit::scripts() !!}`                             |
| `PwaKit::updatePWALogo(Request $request)`              | Handles logo upload, validates, and stores in public & base paths. | `$result = PwaKit::updatePWALogo($request);`            |
| `createOrUpdate(array $manifest, bool $force = false)` | Programmatically create or update `manifest.json`.                 | `$pwa = new PwaKit(); $pwa->createOrUpdate($manifest);` |
| `update(array $manifestData)`                          | Updates manifest data safely.                                      | `$pwa = new PwaKit(); $pwa->update($manifestData);`     |

---

## 🛠️ Major Methods Usage Examples

### 1. `PwaKit::head()`

Generates all `<meta>`, `<link>` and stylesheet tags required for your PWA.

```blade
{{-- In the <head> section of your Blade template --}}
{!! PwaKit::head() !!}
```

---

### 2. `PwaKit::scripts()`

Registers the service worker, required scripts, and displays the install toast.

```blade
{{-- Before closing </body> tag --}}
{!! PwaKit::scripts() !!}
```

---

### 3. `PwaKit::updatePWALogo(Request $request)`

Handles logo upload, validates dimensions and type, and stores the logo in **public** & **base** directories.

```php
use Illuminate\Http\Request;
use Devrabiul\PwaKit\Facades\PwaKit;

public function updateLogo(Request $request)
{
    $result = PwaKit::updatePWALogo($request);

    if ($result['status']) {
        return back()->with('success', $result['message']);
    } else {
        return back()->withErrors($result['errors'] ?? $result['error']);
    }
}
```

---

### 4. `createOrUpdate(array $manifest, bool $force = false)`

Programmatically creates or updates the `manifest.json` file.

```php
use Devrabiul\PwaKit\PwaKit;

$pwa = new PwaKit();

$manifest = [
    'name' => 'My Awesome PWA',
    'short_name' => 'AwesomePWA',
    'start_url' => '/',
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#ff5733',
];

$pwa->createOrUpdate($manifest, true); // true = force overwrite
```

---

### 5. `update(array $manifestData)`

Safely updates manifest data without overwriting other existing keys.

```php
use Devrabiul\PwaKit\PwaKit;

$pwa = new PwaKit();

$updatedManifest = [
    'theme_color' => '#00aaff',
    'background_color' => '#f0f0f0',
];

$pwa->update($updatedManifest);
```

---


## 💡 Benefits

* ✅ Turn your Laravel web app into an **installable PWA** instantly.
* ✅ Provides **offline support**, caching, and fast load times.
* ✅ Reduces repetitive boilerplate code for **service workers & manifest**.
* ✅ Fully **customizable** via configuration.
* ✅ Works seamlessly with **Blade, Livewire, Vue 3, and React**.
* ✅ Encourages modern web best practices.

---

## 🔧 Commands

* **Update Manifest:**

```bash
php artisan pwa:update-manifest
```

---

## ⚠️ Requirements

* Laravel 8.x to 12.x
* PHP 8.0+
* HTTPS (PWAs require secure contexts for service workers)
* Optional: Livewire v3 for SPA support

---

## 🌱 Treeware

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**](https://plant.treeware.earth/devrabiul/laravel-pwa-kit) to thank us for our work. By contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.


---

## 🤝 Contributing

1. Fork the repository.
2. Make your changes.
3. Submit a pull request.

Feature requests and bugs? [Open an issue](https://github.com/devrabiul/laravel-pwa-kit/issues).

---

## 📄 License

MIT License – see [LICENSE](LICENSE) file.

---

## 📬 Contact

For support:
📧 Email: [devrabiul@gmail.com](mailto:devrabiul@gmail.com)
🌐 GitHub: [devrabiul/laravel-pwa-kit](https://github.com/devrabiul/laravel-pwa-kit)