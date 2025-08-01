document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('yak-popups-config');
    if (!configEl) {
        console.warn('[Yak Popups] No config found.');
        return;
    }

    const config = JSON.parse(configEl.textContent);
    const popup = document.getElementById('yak-popup');
    if (!popup) return;

    const closeBtn = popup.querySelector('.yak-popup__close');
    const overlay = popup.querySelector('.yak-popup__overlay');
    const storageKey = 'yakPopupDismissed';
    const now = Date.now();

    // Check if current user is an admin (body class injected via PHP)
    const isAdmin = document.body.classList.contains('role-administrator');

    // Helpers: get/set localStorage with expiry
    function getStored() {
        const data = localStorage.getItem(storageKey);
        if (!data) return null;
        try {
            const parsed = JSON.parse(data);
            if (parsed.expire && parsed.expire > now) {
                return parsed;
            } else {
                localStorage.removeItem(storageKey);
                return null;
            }
        } catch (e) {
            return null;
        }
    }

    function setStored(days) {
        const expire = now + days * 24 * 60 * 60 * 1000;
        localStorage.setItem(storageKey, JSON.stringify({ expire }));
    }

    // Handle Test Mode
    if (config.show_test && isAdmin) {
        console.info('[Yak Popups] Test mode enabled — forcing popup for admin.');
    } else {
        // Bail if already dismissed
        if (getStored()) {
            console.info('[Yak Popups] Popup previously dismissed.');
            return;
        }
    }

    // Show popup
    function showPopup() {
        popup.removeAttribute('hidden');
        document.body.classList.add('yak-popup--active');
        console.info('[Yak Popups] Popup shown.');
    }

    // Hide popup + store dismissal
    function hidePopup() {
        popup.setAttribute('hidden', 'true');
        document.body.classList.remove('yak-popup--active');

        // Only set storage if not in admin Test Mode
        if (!(config.show_test && isAdmin)) {
            setStored(config.dismiss_days || 7);
            console.info('[Yak Popups] Popup dismissed (saved).');
        } else {
            console.info('[Yak Popups] Popup dismissed (Test Mode — not saved).');
        }
    }

    // Bind close actions
    closeBtn?.addEventListener('click', hidePopup);
    overlay?.addEventListener('click', hidePopup);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') hidePopup();
    });

    // Trigger logic
    if (config.trigger === 'load') {
        showPopup();
    } else if (config.trigger === 'delay') {
        const delay = (config.delay || 5) * 1000;
        setTimeout(showPopup, delay);
    } else if (config.trigger === 'scroll') {
        let shown = false;
        window.addEventListener('scroll', () => {
            if (shown) return;
            const scrollPercent = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;
            if (scrollPercent > 50) { // default 50%
                showPopup();
                shown = true;
            }
        });
    }
});
