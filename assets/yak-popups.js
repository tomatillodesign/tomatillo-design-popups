document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('yak-popups-config');
    if (!configEl) {
        // Always show this warning as it indicates a configuration issue
        console.warn('[Yak Popups] No config found.');
        return;
    }

    const config = JSON.parse(configEl.textContent);
    const popup = document.getElementById('yak-popup');
    if (!popup) return;

    const closeBtn = popup.querySelector('.yak-popup__close');
    const overlay = popup.querySelector('.yak-popup__overlay');
    const storageKey = 'yak_popups_dismissed_v1_2';
    const now = Date.now();
    const isAdmin = document.body.classList.contains('role-administrator');
    const isTestMode = config.show_test && isAdmin;

    // Console logging helper - only log in test mode
    const log = (message, type = 'info') => {
        if (isTestMode) {
            console[type](`[Yak Popups] ${message}`);
        }
    };

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
        log('Popup shown.');

        const gfWrappers = popup.querySelectorAll('.gform_wrapper');
        if (gfWrappers.length) {
            const fixGF = (attempts = 0) => {
                gfWrappers.forEach((wrapper, i) => {
                    const computedStyle = window.getComputedStyle(wrapper);
                    if (computedStyle.display === 'none') {
                        wrapper.style.display = 'block';
                        log(`GF wrapper #${i + 1} forced visible.`);
                    }

                    const forms = wrapper.querySelectorAll('form[id^="gform_"]');
                    forms.forEach(form => {
                        const formId = form.getAttribute('data-formid');
                        if (formId) {
                            // Fire Gravity Forms hooks
                            if (typeof gform !== 'undefined' && typeof gform.doAction === 'function') {
                                gform.doAction('gform_post_render', formId, formId);
                                log(`gform_post_render triggered for form ${formId}.`);
                            }

                            // Apply conditional logic if available
                            if (typeof window.gf_apply_rules === 'function') {
                                window.gf_apply_rules(formId, [], true);
                                log(`gf_apply_rules applied for form ${formId}.`);
                            } else {
                                log('gf_apply_rules not available yet.', 'warn');
                            }
                        }
                    });
                });

                // Retry if GF hasn't fully initialized
                if (attempts < 5) {
                    setTimeout(() => fixGF(attempts + 1), 300);
                } else {
                    log('GF initialization attempts complete.');
                }
            };

            fixGF();
        }
    }

    function hidePopup() {
        popup.classList.remove('yak-popup--active');
        document.body.classList.remove('yak-popup--active');
        if (!(config.show_test && isAdmin)) {
            setStored(config.dismiss_days || 7);
            log('Popup dismissed (saved).');
        } else {
            log('Popup dismissed (Test Mode — not saved).');
        }
    }

    // Test mode override
    if (config.show_test && isAdmin) {
        log('Test mode enabled — forcing popup for admin.');
    } else if (getStored()) {
        log('Popup previously dismissed.');
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
        const scrollThreshold = config.scroll_percent || 50;
        
        window.addEventListener('scroll', () => {
            if (shown) return;
            const scrollPercent = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;
            if (scrollPercent > scrollThreshold) {
                showPopup();
                shown = true;
                log(`Popup triggered at ${Math.round(scrollPercent)}% scroll (threshold: ${scrollThreshold}%)`);
            }
        });
    }
});
