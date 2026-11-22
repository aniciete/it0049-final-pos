/**
 * Bright-Market POS client interactions.
 * Handles responsive popover navigation, native confirmation dialogs,
 * password visibility toggles, custom file picker triggers, quantity steppers,
 * dismissible flash banners, live image previews, and checkout summary preview.
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

    function initResponsiveNavbar() {
        const header = document.querySelector('.site-header');
        const toggleBtn = document.querySelector('.nav-menu-toggle');
        const navMenu = document.getElementById('main-nav-menu');
        if (!header) {
            return;
        }

        const syncHeaderHeight = () => {
            document.documentElement.style.setProperty('--header-h', `${header.offsetHeight}px`);
        };

        if (!toggleBtn || !navMenu) {
            syncHeaderHeight();
            return;
        }

        let currentWide = null;

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
            const sessionZone = header.querySelector('.header-session-zone');
            const navRect = navMenu.getBoundingClientRect();
            const sessionRect = sessionZone ? sessionZone.getBoundingClientRect() : null;
            const collidesWithSession = Boolean(
                sessionRect && navRect.width > 0 && navRect.right > sessionRect.left - 8
            );
            const overflows = headerInner
                ? headerInner.scrollWidth > headerInner.clientWidth + 1
                : header.scrollWidth > header.clientWidth + 1;

            const shouldBeWide = !overflows && !collidesWithSession && window.innerWidth >= 980;
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

    function initFlashDismiss() {
        document.querySelectorAll('[data-dismiss-alert]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const alert = btn.closest('.alert');
                if (alert) {
                    alert.remove();
                }
            });
        });

        document.querySelectorAll('.alert[data-auto-dismiss]').forEach((alert) => {
            const delay = parseInt(alert.getAttribute('data-auto-dismiss') || '0', 10);
            if (delay > 0) {
                window.setTimeout(() => {
                    if (alert.isConnected) {
                        alert.remove();
                    }
                }, delay);
            }
        });
    }

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
                button.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');

                const showIcon = button.querySelector('[data-icon-show]');
                const hideIcon = button.querySelector('[data-icon-hide]');
                if (showIcon && hideIcon) {
                    showIcon.hidden = reveal;
                    hideIcon.hidden = !reveal;
                } else {
                    button.textContent = reveal ? 'Hide' : 'Show';
                }
            });
        });
    }

    function initSubmitBusyState() {
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || event.defaultPrevented) {
                return;
            }
            if (form.dataset.confirmTitle && form.dataset.confirmed !== 'true') {
                return;
            }

            const formId = form.getAttribute('id');
            const submitBtn = form.querySelector('button[type="submit"][data-busy-text]')
                || (formId ? document.querySelector(`button[type="submit"][form="${formId}"][data-busy-text]`) : null);
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

    function initImagePreviews() {
        document.querySelectorAll('[data-file-trigger]').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                const inputId = trigger.getAttribute('data-file-trigger');
                const input = inputId ? document.getElementById(inputId) : null;
                if (input instanceof HTMLInputElement) {
                    input.click();
                }
            });
        });

        document.querySelectorAll('input[type="file"][data-preview-img]').forEach((input) => {
            if (!(input instanceof HTMLInputElement)) {
                return;
            }
            const previewImg = document.getElementById(input.dataset.previewImg || '');
            const statusEl = document.getElementById(input.dataset.previewStatus || '');
            const mirrorImg = input.id === 'product-image'
                ? document.getElementById('product-preview-card-img')
                : null;
            const removeCheckbox = input.dataset.removeCheckbox
                ? document.getElementById(input.dataset.removeCheckbox)
                : null;

            if (!(previewImg instanceof HTMLImageElement)) {
                return;
            }

            const defaultSrc = previewImg.dataset.defaultSrc || previewImg.src;
            const defaultStatus = statusEl ? statusEl.textContent.trim() : '';
            let activeUrl = null;

            const setPreviewSrc = (src) => {
                previewImg.src = src;
                if (mirrorImg instanceof HTMLImageElement) {
                    mirrorImg.src = src;
                }
            };

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
                    setPreviewSrc(defaultSrc);
                    if (statusEl) {
                        statusEl.textContent = defaultStatus;
                    }
                    return;
                }

                if (file.type && !ALLOWED_IMAGE_TYPES.has(file.type)) {
                    input.setCustomValidity('Please select a JPG, PNG, or WebP image file.');
                    setPreviewSrc(defaultSrc);
                    return;
                }

                if (file.size > MAX_IMAGE_BYTES) {
                    input.setCustomValidity('Image file must not exceed 2 MB.');
                    setPreviewSrc(defaultSrc);
                    return;
                }

                if (removeCheckbox instanceof HTMLInputElement) {
                    removeCheckbox.checked = false;
                }

                activeUrl = URL.createObjectURL(file);
                setPreviewSrc(activeUrl);

                if (statusEl) {
                    const kb = Math.max(1, Math.round(file.size / 1024));
                    statusEl.textContent = `Selected: ${file.name} (${kb} KB)`;
                }
            });
        });
    }

    function initSalePreview() {
        const productSelect = document.getElementById('sale-product');
        const qtyInput = document.getElementById('sale-quantity');
        const priceOut = document.getElementById('preview-unit-price');
        const remainingOut = document.getElementById('preview-remaining-stock');
        const totalOut = document.getElementById('preview-order-total');
        const warningEl = document.getElementById('preview-stock-warning');
        const decBtn = document.getElementById('qty-decrement');
        const incBtn = document.getElementById('qty-increment');
        const recoveryBtn = document.getElementById('qty-set-max');

        if (
            !(productSelect instanceof HTMLSelectElement) ||
            !(qtyInput instanceof HTMLInputElement) ||
            !priceOut || !remainingOut || !totalOut || !warningEl
        ) {
            return;
        }

        let serverInvalidQty = qtyInput.getAttribute('aria-invalid') === 'true';
        const initialQtyValue = qtyInput.value;

        const qtyErrorEl = document.getElementById('sale_quantity_error');
        const formErrorSummary = document.getElementById('form-error-summary');
        const clearServerOversell = () => {
            serverInvalidQty = false;
            if (qtyErrorEl) {
                qtyErrorEl.hidden = true;
            }
            if (formErrorSummary) {
                formErrorSummary.hidden = true;
            }
            if (recoveryBtn) {
                recoveryBtn.hidden = true;
            }
        };

        if (serverInvalidQty) {
            qtyInput.focus();
        }

        const updateSummary = (isUserEdit = false) => {
            if (isUserEdit && qtyInput.value !== initialQtyValue) {
                clearServerOversell();
            }

            const option = productSelect.options[productSelect.selectedIndex];
            if (!option || !option.value) {
                priceOut.textContent = '$0.00';
                remainingOut.textContent = 'Select product';
                remainingOut.classList.remove('mono-num', 'is-oversell');
                totalOut.textContent = '$0.00';
                warningEl.hidden = true;
                qtyInput.removeAttribute('max');
                qtyInput.setAttribute('aria-invalid', serverInvalidQty ? 'true' : 'false');
                return;
            }

            const price = parseFloat(option.dataset.price || '0');
            const stock = parseInt(option.dataset.stock || '0', 10);
            qtyInput.setAttribute('max', String(stock));

            const rawTrimmed = qtyInput.value.trim();
            const parsedNum = Number(rawTrimmed);
            const isWholePositive = rawTrimmed !== '' && Number.isInteger(parsedNum) && parsedNum > 0;
            const validQty = isWholePositive ? parsedNum : 0;
            const remaining = stock - validQty;
            const total = price * validQty;

            priceOut.textContent = '$' + price.toFixed(2);
            remainingOut.textContent = validQty > 0 ? String(remaining) : String(stock);
            remainingOut.classList.add('mono-num');
            totalOut.textContent = '$' + total.toFixed(2);

            const oversell = validQty > stock;
            remainingOut.classList.toggle('is-oversell', oversell);
            warningEl.hidden = !oversell || (serverInvalidQty && qtyErrorEl && !qtyErrorEl.hidden);

            const isInvalid = oversell || (rawTrimmed !== '' && !isWholePositive);
            qtyInput.setAttribute('aria-invalid', (isInvalid || serverInvalidQty) ? 'true' : 'false');
        };

        const stepQuantity = (delta) => {
            const current = parseInt(qtyInput.value || '1', 10);
            const maxVal = parseInt(qtyInput.getAttribute('max') || '999999', 10);
            const base = Number.isNaN(current) ? 1 : current + delta;
            const next = Math.max(1, Math.min(maxVal, base));
            qtyInput.value = String(next);
            clearServerOversell();
            updateSummary(true);
        };

        decBtn?.addEventListener('click', () => stepQuantity(-1));
        incBtn?.addEventListener('click', () => stepQuantity(1));

        recoveryBtn?.addEventListener('click', () => {
            const maxVal = recoveryBtn.dataset.maxStock || qtyInput.getAttribute('max') || '1';
            qtyInput.value = maxVal;
            clearServerOversell();
            updateSummary(true);
            qtyInput.focus();
        });

        productSelect.addEventListener('change', () => {
            clearServerOversell();
            updateSummary(true);
        });
        qtyInput.addEventListener('input', () => updateSummary(true));
        updateSummary(false);
    }

    function initDemoLoginFill() {
        document.querySelectorAll('[data-fill-demo-login]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const container = btn.closest('.auth-card') || document;
                const loginInput = container.querySelector('input[name="login"]');
                const passwordInput = container.querySelector('input[name="password"]');

                if (loginInput instanceof HTMLInputElement) {
                    loginInput.value = btn.dataset.demoUser || '';
                }
                if (passwordInput instanceof HTMLInputElement) {
                    passwordInput.value = btn.dataset.demoPass || '';
                }
                if (loginInput instanceof HTMLInputElement) {
                    loginInput.focus();
                }
            });
        });
    }

    function initProductPreview() {
        const nameInput = document.getElementById('product-name');
        const priceInput = document.getElementById('product-price');
        const stockInput = document.getElementById('product-stock');
        const nameOut = document.getElementById('product-preview-card-name');
        const priceOut = document.getElementById('product-preview-card-price');
        const stockOut = document.getElementById('product-preview-card-stock');

        if (!nameInput || !priceInput || !stockInput || !nameOut || !priceOut || !stockOut) {
            return;
        }

        const render = () => {
            const name = nameInput.value.trim();
            nameOut.textContent = name !== '' ? name : 'New Product';

            const priceVal = parseFloat(priceInput.value);
            priceOut.textContent = '$' + (Number.isFinite(priceVal) && priceVal > 0 ? priceVal : 0).toFixed(2);

            const stockVal = parseInt(stockInput.value, 10);
            const qty = Number.isFinite(stockVal) ? Math.max(0, stockVal) : 0;
            if (qty <= 0) {
                stockOut.className = 'status-pill status-out';
                stockOut.textContent = 'Out of stock';
            } else if (qty <= 5) {
                stockOut.className = 'status-pill status-low';
                stockOut.textContent = `${qty} low stock`;
            } else {
                stockOut.className = 'cell-muted';
                stockOut.textContent = `${qty} in stock`;
            }
        };

        nameInput.addEventListener('input', render);
        priceInput.addEventListener('input', render);
        stockInput.addEventListener('input', render);
        render();
    }

    initResponsiveNavbar();
    initFlashDismiss();
    initConfirmDialog();
    initPasswordToggles();
    initSubmitBusyState();
    initImagePreviews();
    initProductPreview();
    initSalePreview();
    initDemoLoginFill();
})();
