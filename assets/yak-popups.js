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
    const isAdmin = document.body.classList.contains('role-administrator');

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
        } catch {
            return null;
        }
    }

    function setStored(days) {
        const expire = now + days * 24 * 60 * 60 * 1000;
        localStorage.setItem(storageKey, JSON.stringify({ expire }));
    }


    
function showPopup() {
    popup.classList.add('yak-popup--active');
    document.body.classList.add('yak-popup--active');
    console.info('[Yak Popups] Popup shown.');

    const gfWrappers = popup.querySelectorAll('.gform_wrapper');
    if (gfWrappers.length) {
        console.info(`[Yak Popups] Found ${gfWrappers.length} Gravity Forms wrapper(s) inside popup.`);

        const fixGF = (attempts = 0) => {
            gfWrappers.forEach((wrapper, i) => {
                const computedStyle = window.getComputedStyle(wrapper);

                // Ensure wrapper is visible
                if (computedStyle.display === 'none') {
                    wrapper.style.display = 'block';
                    console.info(`[Yak Popups] GF wrapper #${i + 1} forced visible.`);
                }

                // Log the number of hidden conditional fields for debugging
                const hiddenFields = wrapper.querySelectorAll('.gfield[style*="display: none"]');
                console.info(`[Yak Popups] GF wrapper #${i + 1} contains ${hiddenFields.length} hidden field(s).`);

                const conditionallyHidden = wrapper.querySelectorAll('.gfield.gfield_visibility_hidden');
                if (conditionallyHidden.length) {
                    console.warn(`[Yak Popups] ${conditionallyHidden.length} conditional field(s) currently hidden as expected.`);
                }
            });

            // Trigger Gravity Forms post-render hook
            if (typeof gform !== 'undefined' && typeof gform.doAction === 'function') {
                const forms = popup.querySelectorAll('form[id^="gform_"]');
                forms.forEach(form => {
                    const formId = form.getAttribute('data-formid');
                    if (formId) {
                        gform.doAction('gform_post_render', formId, formId);
                        console.info(`[Yak Popups] gform_post_render triggered for form ${formId}.`);
                    }
                });
            } else {
                console.warn('[Yak Popups] gform object not found — Gravity Forms scripts may not be loaded.');
            }

            // Retry in case GF re-applies display:none
            if (attempts < 5) {
                setTimeout(() => fixGF(attempts + 1), 300);
            } else {
                console.info('[Yak Popups] Completed Gravity Forms wrapper visibility checks.');
            }
        };

        fixGF();
    } else {
        console.warn('[Yak Popups] No Gravity Forms wrapper found inside popup.');
    }
}

    function hidePopup() {
        popup.classList.remove('yak-popup--active');
        document.body.classList.remove('yak-popup--active');
        if (!(config.show_test && isAdmin)) {
            setStored(config.dismiss_days || 7);
            console.info('[Yak Popups] Popup dismissed (saved).');
        } else {
            console.info('[Yak Popups] Popup dismissed (Test Mode — not saved).');
        }
    }

    // Test mode override
    if (config.show_test && isAdmin) {
        console.info('[Yak Popups] Test mode enabled — forcing popup for admin.');
    } else if (getStored()) {
        console.info('[Yak Popups] Popup previously dismissed.');
        return;
    }

    closeBtn?.addEventListener('click', hidePopup);
    overlay?.addEventListener('click', hidePopup);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') hidePopup();
    });

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
            if (scrollPercent > 50) {
                showPopup();
                shown = true;
            }
        });
    }
});
