/**
 * Bright-Market POS client interactions.
 * Handles responsive popover navigation, native confirmation dialogs,
 * password visibility toggles, form submission guards, live image previews,
 * and checkout register total/stock preview.
 */
(() => {
    'use strict';

    const supportsPopover = Object.hasOwn(HTMLElement.prototype, 'popover');
    const MAX_IMAGE_BYTES = 2 * 1024 * 1024;
    const ALLOWED_IMAGE_TYPES = new Set([
        'image/jpeg',
        'image/png',
        'image/webp',
    ]);

    /**
     * Measure header overflow cleanly and toggle between wide inline nav
     * and compact popover menu mode without tearing down an open compact menu.
     */
    function initResponsiveNavbar() {
        const header = document.querySelector('.site-header');
        const toggleBtn = document.querySelector('.nav-menu-toggle');
        const navMenu = document.getElementById('main-nav-menu');
        if (!header || !toggleBtn || !navMenu) {
            return;
        }

        let currentWide = null;

        const syncHeaderHeight = () => {
            document.documentElement.style.setProperty('--header-h', `${header.offsetHeight}px`);
        };

        const applyMode = (isWide) => {
            syncHeaderHeight();
            if (currentWide === isWide) {
                return;
            }
            currentWide = isWide;

            header.classList.add('is-measuring');
            header.classList.toggle('is-wide', isWide);
            header.classList.toggle('is-compact', !isWide);

            if (isWide) {
                if (supportsPopover && navMenu.matches(':popover-open')) {
                    navMenu.hidePopover();
                }
                navMenu.removeAttribute('popover');
                navMenu.classList.remove('is-fallback-open');
                toggleBtn.setAttribute('aria-expanded', 'false');
            } else if (supportsPopover) {
                navMenu.setAttribute('popover', 'auto');
                toggleBtn.setAttribute('popovertarget', 'main-nav-menu');
                toggleBtn.setAttribute('popovertargetaction', 'toggle');
            } else {
                navMenu.removeAttribute('popover');
            }

            requestAnimationFrame(() => {
                header.classList.remove('is-measuring');
                syncHeaderHeight();
            });
        };

        const measure = () => {
            const wasPopoverOpen = supportsPopover && navMenu.matches(':popover-open');
            const wasFallbackOpen = navMenu.classList.contains('is-fallback-open');
            if (wasPopoverOpen || wasFallbackOpen) {
                syncHeaderHeight();
                return;
            }

            header.classList.add('is-measuring');
            header.classList.add('is-wide');
            header.classList.remove('is-compact');
            navMenu.removeAttribute('popover');

            const headerInner = header.querySelector('.header-container');
            const overflows = headerInner
                ? headerInner.scrollWidth > headerInner.clientWidth + 1
                : header.scrollWidth > header.clientWidth + 1;

            const shouldBeWide = !overflows && window.innerWidth >= 980;
            currentWide = null;
            applyMode(shouldBeWide);
        };

        if (supportsPopover) {
            navMenu.addEventListener('toggle', (event) => {
                toggleBtn.setAttribute('aria-expanded', event.newState === 'open' ? 'true' : 'false');
            });
        } else {
            toggleBtn.addEventListener('click', () => {
                const isOpen = navMenu.classList.toggle('is-fallback-open');
                toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        if (typeof ResizeObserver === 'function') {
            const observer = new ResizeObserver(() => measure());
            observer.observe(header);
        } else {
            window.addEventListener('resize', measure, { passive: true });
        }

        if (document.fonts && typeof document.fonts.ready?.then === 'function') {
            document.fonts.ready.then(() => {
                if (!navMenu.matches?.(':popover-open') && !navMenu.classList.contains('is-fallback-open')) {
                    currentWide = null;
                    measure();
                }
            });
        }

        measure();
    }

    /**
     * Progressive enhancement for destructive/archive action forms using native <dialog>.
     */
    function initConfirmDialog() {
        const dialog = document.getElementById('confirm-action-dialog');
        if (!dialog || typeof dialog.showModal !== 'function') {
            return;
        }

        const titleEl = document.getElementById('confirm-dialog-title');
        const messageEl = document.getElementById('confirm-dialog-message');
        const cancelBtn = document.getElementById('confirm-dialog-cancel');
        const confirmBtn = document.getElementById('confirm-dialog-submit');
        let pendingForm = null;

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }
            const title = form.dataset.confirmTitle;
            if (!title || form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();
            pendingForm = form;

            if (titleEl) {
                titleEl.textContent = title;
            }
            if (messageEl) {
                messageEl.textContent = form.dataset.confirmMessage || 'Confirm this ledger update?';
            }
            if (confirmBtn) {
                confirmBtn.textContent = form.dataset.confirmAction || 'Confirm';
                confirmBtn.className = form.dataset.confirmTone === 'primary'
                    ? 'button button-primary'
                    : 'button button-danger';
            }

            dialog.showModal();
        });

        cancelBtn?.addEventListener('click', () => {
            pendingForm = null;
            dialog.close('cancel');
        });

        confirmBtn?.addEventListener('click', () => {
            if (!pendingForm) {
                dialog.close('cancel');
                return;
            }
            const formToSubmit = pendingForm;
            pendingForm = null;
            formToSubmit.dataset.confirmed = 'true';
            dialog.close('confirm');
            if (typeof formToSubmit.requestSubmit === 'function') {
                formToSubmit.requestSubmit();
            } else {
                formToSubmit.submit();
            }
        });

        dialog.addEventListener('cancel', () => {
            pendingForm = null;
        });

        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                pendingForm = null;
                dialog.close('cancel');
            }
        });
    }

    /**
     * Toggle password input visibility with explicit aria-pressed state.
     */
    function initPasswordToggles() {
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('aria-controls') || button.dataset.passwordToggle;
                const input = targetId ? document.getElementById(targetId) : null;
                if (!(input instanceof HTMLInputElement)) {
                    return;
                }

                const reveal = input.type === 'password';
                input.type = reveal ? 'text' : 'password';
                button.setAttribute('aria-pressed', reveal ? 'true' : 'false');
                button.textContent = reveal ? 'Hide' : 'Show';
                button.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
            });
        });
    }

    /**
     * Prevent accidental double clicks from submitting forms multiple times.
     */
    function initSubmitBusyState() {
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || event.defaultPrevented) {
                return;
            }
            if (form.dataset.confirmTitle && form.dataset.confirmed !== 'true') {
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"][data-busy-text]');
            if (!(submitBtn instanceof HTMLButtonElement)) {
                return;
            }

            if (submitBtn.classList.contains('is-busy')) {
                event.preventDefault();
                return;
            }

            const busyText = submitBtn.dataset.busyText;
            if (!busyText) {
                return;
            }

            submitBtn.classList.add('is-busy');
            submitBtn.setAttribute('aria-disabled', 'true');
            window.setTimeout(() => {
                submitBtn.textContent = busyText;
            }, 0);
        });
    }

    /**
     * Live image/avatar upload preview with safe MIME and size feedback.
     */
    function initImagePreviews() {
        document.querySelectorAll('input[type="file"][data-preview-img]').forEach((input) => {
            if (!(input instanceof HTMLInputElement)) {
                return;
            }
            const previewImg = document.getElementById(input.dataset.previewImg || '');
            const statusEl = document.getElementById(input.dataset.previewStatus || '');
            const removeCheckbox = input.dataset.removeCheckbox
                ? document.getElementById(input.dataset.removeCheckbox)
                : null;

            if (!(previewImg instanceof HTMLImageElement)) {
                return;
            }

            const defaultSrc = previewImg.dataset.defaultSrc || previewImg.src;
            const defaultStatus = statusEl ? statusEl.textContent.trim() : '';
            let activeUrl = null;

            const revoke = () => {
                if (activeUrl) {
                    URL.revokeObjectURL(activeUrl);
                    activeUrl = null;
                }
            };

            window.addEventListener('pagehide', revoke);

            input.addEventListener('change', () => {
                revoke();
                input.setCustomValidity('');

                const file = input.files && input.files[0];
                if (!file) {
                    previewImg.src = defaultSrc;
                    if (statusEl) {
                        statusEl.textContent = defaultStatus;
                    }
                    return;
                }

                if (file.type && !ALLOWED_IMAGE_TYPES.has(file.type)) {
                    input.setCustomValidity('Please select a JPG, PNG, or WebP image file.');
                    previewImg.src = defaultSrc;
                    return;
                }

                if (file.size > MAX_IMAGE_BYTES) {
                    input.setCustomValidity('Image file must not exceed 2 MB.');
                    previewImg.src = defaultSrc;
                    return;
                }

                if (removeCheckbox instanceof HTMLInputElement) {
                    removeCheckbox.checked = false;
                }

                activeUrl = URL.createObjectURL(file);
                previewImg.src = activeUrl;

                if (statusEl) {
                    const kb = Math.max(1, Math.round(file.size / 1024));
                    statusEl.textContent = `Ready to upload: ${file.name} (${kb} KB)`;
                }
            });
        });
    }

    /**
     * Live receipt summary updates on Record Sale page.
     */
    function initSalePreview() {
        const productSelect = document.getElementById('sale-product');
        const qtyInput = document.getElementById('sale-quantity');
        const nameOut = document.getElementById('preview-product-name');
        const priceOut = document.getElementById('preview-unit-price');
        const stockOut = document.getElementById('preview-available-stock');
        const remainingOut = document.getElementById('preview-remaining-stock');
        const totalOut = document.getElementById('preview-order-total');
        const warningEl = document.getElementById('preview-stock-warning');

        if (
            !(productSelect instanceof HTMLSelectElement) ||
            !(qtyInput instanceof HTMLInputElement) ||
            !nameOut || !priceOut || !stockOut || !remainingOut || !totalOut || !warningEl
        ) {
            return;
        }

        let serverInvalidQty = qtyInput.getAttribute('aria-invalid') === 'true';
        const initialQtyValue = qtyInput.value;

        const updateSummary = (isUserEdit = false) => {
            if (isUserEdit && qtyInput.value !== initialQtyValue) {
                serverInvalidQty = false;
            }

            const option = productSelect.options[productSelect.selectedIndex];
            if (!option || !option.value) {
                nameOut.textContent = 'None selected';
                priceOut.textContent = '$0.00';
                stockOut.textContent = 'Select product';
                stockOut.classList.remove('mono-num');
                remainingOut.textContent = 'Select product';
                remainingOut.classList.remove('mono-num', 'is-oversell');
                totalOut.textContent = '$0.00';
                warningEl.hidden = true;
                qtyInput.setAttribute('aria-invalid', serverInvalidQty ? 'true' : 'false');
                return;
            }

            const price = parseFloat(option.dataset.price || '0');
            const stock = parseInt(option.dataset.stock || '0', 10);
            const rawTrimmed = qtyInput.value.trim();
            const parsedNum = Number(rawTrimmed);
            const isWholePositive = rawTrimmed !== '' && Number.isInteger(parsedNum) && parsedNum > 0;
            const validQty = isWholePositive ? parsedNum : 0;
            const remaining = stock - validQty;
            const total = price * validQty;

            nameOut.textContent = option.dataset.name || option.textContent || 'Selected product';
            priceOut.textContent = '$' + price.toFixed(2);
            stockOut.textContent = String(stock);
            stockOut.classList.add('mono-num');
            remainingOut.textContent = validQty > 0 ? String(remaining) : String(stock);
            remainingOut.classList.add('mono-num');
            totalOut.textContent = '$' + total.toFixed(2);

            const oversell = validQty > stock;
            remainingOut.classList.toggle('is-oversell', oversell);
            warningEl.hidden = !oversell;

            const isInvalid = oversell || (rawTrimmed !== '' && !isWholePositive);
            if (isInvalid || serverInvalidQty) {
                qtyInput.setAttribute('aria-invalid', 'true');
            } else {
                qtyInput.setAttribute('aria-invalid', 'false');
            }
        };

        productSelect.addEventListener('change', () => {
            serverInvalidQty = false;
            updateSummary(true);
        });
        qtyInput.addEventListener('input', () => updateSummary(true));
        updateSummary(false);
    }

    initResponsiveNavbar();
    initConfirmDialog();
    initPasswordToggles();
    initSubmitBusyState();
    initImagePreviews();
    initSalePreview();
})();
