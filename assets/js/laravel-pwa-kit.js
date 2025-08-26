let deferredPrompt = null;

function isToastShown() {
    const lastShown = localStorage.getItem("pwaToastShown");
    if (!lastShown) return false;
    const lastDate = new Date(parseInt(lastShown, 10));
    const now = new Date();
    return (
        lastDate.getDate() === now.getDate() &&
        lastDate.getMonth() === now.getMonth() &&
        lastDate.getFullYear() === now.getFullYear()
    );
}

function showInstallPromotion() {
    const toast = document.getElementById("install-prompt");
    if (!toast) return;
    toast.style.display = "flex";
    requestAnimationFrame(() => {
        toast.classList.remove("toast-slide-out");
        toast.classList.add("toast-slide-in");
    });
}

function hideInstallPromotion() {
    const toast = document.getElementById("install-prompt");
    if (!toast) return;
    toast.classList.remove("toast-slide-in");
    toast.classList.add("toast-slide-out");
    setTimeout(() => {
        toast.style.display = "none";
    }, 300);
}

async function triggerPWAInstall() {
    if (!deferredPrompt) {
        console.warn("No PWA install prompt available");
        hideInstallPromotion();
        return;
    }

    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log("PWA install choice:", outcome);

    if (outcome === "accepted") {
        localStorage.setItem("pwaInstalled", "true");
    }

    deferredPrompt = null;
    hideInstallPromotion();
}

function bindInstallButtons() {
    // Single install button
    const installBtn = document.getElementById("installPWAButton");
    if (installBtn) {
        installBtn.onclick = triggerPWAInstall;
    }

    // Multiple forced install buttons
    document.querySelectorAll(".force-install-pwa-app").forEach((btn) => {
        btn.onclick = triggerPWAInstall;
    });

    // Close button
    const closeBtn = document.getElementById("install-pwa-button-close");
    if (closeBtn) closeBtn.onclick = hideInstallPromotion;
}

// ==========================
// Events
// ==========================

// PWA prompt
window.addEventListener("beforeinstallprompt", (e) => {
    e.preventDefault();
    deferredPrompt = e;

    if (!isToastShown() && !window.matchMedia("(display-mode: standalone)").matches) {
        setTimeout(() => {
            showInstallPromotion();
            localStorage.setItem("pwaToastShown", Date.now());
        }, 1000);
    }
});

// App installed
window.addEventListener("appinstalled", () => {
    deferredPrompt = null;
    hideInstallPromotion();
    console.log("PWA installed successfully ✅");
});

// Re-bind buttons on DOM ready + SPA reloads
document.addEventListener("DOMContentLoaded", bindInstallButtons);
document.addEventListener("livewire:navigated", bindInstallButtons);
