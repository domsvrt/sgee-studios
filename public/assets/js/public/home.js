(function () {
    var feedbackMessages = document.querySelectorAll('[data-auto-dismiss-feedback]');
    if (feedbackMessages.length === 0) return;

    function dismissFeedback(message) {
        if (!message || message.dataset.dismissed === '1') return;
        message.dataset.dismissed = '1';
        message.style.overflow = 'hidden';
        message.style.height = message.offsetHeight + 'px';
        message.style.transition = 'height 220ms ease, opacity 220ms ease, margin 220ms ease, padding 220ms ease';
        requestAnimationFrame(function () {
            message.style.opacity = '0';
            message.style.height = '0px';
            message.style.marginTop = '0';
            message.style.paddingTop = '0';
            message.style.paddingBottom = '0';
        });
        setTimeout(function () {
            if (message && message.remove) message.remove();
        }, 240);
    }

    feedbackMessages.forEach(function (message) {
        var closeButton = message.querySelector('[data-dismiss-feedback]');
        if (closeButton) {
            closeButton.addEventListener('click', function () {
                dismissFeedback(message);
            });
        }

        var progress = message.querySelector('[data-feedback-progress]');
        if (progress) {
            progress.style.transition = 'width 4s linear';
            progress.style.width = '100%';
            requestAnimationFrame(function () {
                progress.style.width = '0%';
            });
        }

        setTimeout(function () {
            dismissFeedback(message);
        }, 4000);
    });
})();

(function () {
    var mobileInputs = document.querySelectorAll('[data-ph-mobile]');
    mobileInputs.forEach(function (input) {
        function normalize() {
            input.value = String(input.value || '').replace(/\D+/g, '').slice(0, 11);
        }
        input.addEventListener('input', normalize);
        input.addEventListener('paste', function () {
            setTimeout(normalize, 0);
        });
        normalize();
    });
})();

(function () {
    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-auth-password-toggle]');
        if (!toggle) return;
        var wrap = toggle.parentElement;
        if (!wrap) return;
        var input = wrap.querySelector('[data-auth-password-input]');
        if (!input) return;
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        var eyeOpen = toggle.querySelector('[data-eye-open]');
        var eyeClosed = toggle.querySelector('[data-eye-closed]');
        if (eyeOpen) eyeOpen.classList.toggle('hidden', show);
        if (eyeClosed) eyeClosed.classList.toggle('hidden', !show);
    });
})();

(function () {
    var root = document.querySelector('[data-book-now-catalog]');
    if (!root) return;

    var catalog = [];
    try {
        catalog = JSON.parse(root.getAttribute('data-book-now-catalog') || '[]');
    } catch (error) {
        catalog = [];
    }

    var categoriesWrap = document.getElementById('book-now-categories');
    var servicesWrap = document.getElementById('book-now-services');
    var formWrap = document.getElementById('book-now-form');
    var servicesList = document.getElementById('book-now-services-list');
    var categoryTitle = document.getElementById('book-now-category-title');
    var categoryDesc = document.getElementById('book-now-category-desc');
    var selectedCount = document.getElementById('book-now-selected-count');
    var totalEl = document.getElementById('book-now-estimated-total');
    var summaryEl = document.getElementById('book-now-selected-summary');
    var formTotalEl = document.getElementById('book-now-form-total');
    var categoryInput = document.getElementById('book-now-category-id');
    var serviceInputsWrap = document.getElementById('book-now-service-inputs');
    var isAuthenticated = root.getAttribute('data-book-now-authenticated') === '1';
    var selectedCategoryId = 0;
    var selectedItems = {};

    function getCategory() {
        return catalog.find(function (row) { return Number(row.id) === Number(selectedCategoryId); }) || null;
    }

    function formatPhp(amount) {
        return 'PHP ' + Number(amount || 0).toFixed(2);
    }

    function updateTotals() {
        var keys = Object.keys(selectedItems);
        var total = 0;
        keys.forEach(function (key) {
            var row = selectedItems[key];
            total += Number(row.price || 0) * Number(row.qty || 1);
        });
        selectedCount.textContent = keys.length + ' item' + (keys.length === 1 ? '' : 's') + ' selected';
        totalEl.textContent = formatPhp(total);
        summaryEl.textContent = keys.length > 0
            ? 'Selected services: ' + keys.map(function (key) { return selectedItems[key].name + ' (x' + selectedItems[key].qty + ')'; }).join(' | ')
            : 'No services selected yet.';
        if (formTotalEl) formTotalEl.textContent = formatPhp(total);
        if (serviceInputsWrap) {
            serviceInputsWrap.innerHTML = '';
            keys.forEach(function (key) {
                var serviceId = Number(selectedItems[key].id || 0);
                if (serviceId <= 0) return;
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'service_ids[]';
                input.value = String(serviceId);
                serviceInputsWrap.appendChild(input);
            });
        }
        if (categoryInput) categoryInput.value = selectedCategoryId ? String(selectedCategoryId) : '';
    }

    function setSelection(item, mode, sectionCode) {
        if (mode === 'single') {
            Object.keys(selectedItems).forEach(function (key) {
                if (selectedItems[key].sectionCode === sectionCode) {
                    delete selectedItems[key];
                }
            });
            selectedItems[item.code] = { id: Number(item.id || 0), name: item.name, price: Number(item.price || 0), qty: 1, sectionCode: sectionCode };
            return;
        }

        if (selectedItems[item.code]) {
            delete selectedItems[item.code];
        } else {
            selectedItems[item.code] = { id: Number(item.id || 0), name: item.name, price: Number(item.price || 0), qty: 1, sectionCode: sectionCode };
        }
    }

    function renderServices() {
        var category = getCategory();
        if (!category) return;

        categoryTitle.textContent = category.name || '';
        categoryDesc.textContent = category.description || '';
        servicesList.innerHTML = '';

        (category.sections || []).forEach(function (section) {
            var sectionWrap = document.createElement('div');
            var header = '<h3 class="text-xl font-black text-slate-950">' + (section.name || '') + '</h3>';
            if (section.description) {
                header += '<p class="mt-1 text-xs text-slate-500">' + section.description + '</p>';
            }
            var items = '<div class="mt-3 space-y-3">';
            (section.items || []).forEach(function (item) {
                var selected = !!selectedItems[item.code];
                items += '<button type="button" class="w-full rounded-lg border p-4 text-left transition ' + (selected ? 'border-slate-950 bg-slate-50' : 'border-slate-200 bg-white hover:border-slate-400') + '" data-select-item="' + item.code + '" data-section-type="' + (section.selection_type || 'multiple') + '" data-section-code="' + section.id + '">';
                items += '<div class="flex items-start justify-between gap-4"><div><p class="text-sm font-black text-slate-900">' + (item.name || '') + '</p>';
                if (item.description) items += '<p class="mt-1 text-xs text-slate-500">' + item.description + '</p>';
                items += '</div><div class="text-right"><p class="text-lg font-black text-slate-950">' + formatPhp(item.price) + '</p>';
                items += '</div></div></button>';
            });
            items += '</div>';
            sectionWrap.innerHTML = header + items;
            servicesList.appendChild(sectionWrap);
        });
        updateTotals();
    }

    categoriesWrap.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-category-id]');
        if (!btn) return;
        selectedCategoryId = Number(btn.getAttribute('data-category-id') || '0');
        selectedItems = {};
        categoriesWrap.classList.add('hidden');
        formWrap.classList.add('hidden');
        servicesWrap.classList.remove('hidden');
        renderServices();
    });

    servicesWrap.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-select-item]');
        if (!btn) return;
        var code = btn.getAttribute('data-select-item') || '';
        var sectionType = btn.getAttribute('data-section-type') || 'multiple';
        var sectionCode = btn.getAttribute('data-section-code') || '';
        var category = getCategory();
        if (!category) return;
        var itemFound = null;
        (category.sections || []).forEach(function (section) {
            (section.items || []).forEach(function (item) {
                if (item.code === code) itemFound = item;
            });
        });
        if (!itemFound) return;
        setSelection(itemFound, sectionType, sectionCode);
        renderServices();
    });

    var backBtn = document.getElementById('book-now-back');
    if (backBtn) {
        backBtn.addEventListener('click', function () {
            servicesWrap.classList.add('hidden');
            formWrap.classList.add('hidden');
            categoriesWrap.classList.remove('hidden');
        });
    }

    var continueBtn = document.getElementById('book-now-continue');
    if (continueBtn) {
        continueBtn.addEventListener('click', function () {
            if (!isAuthenticated) {
                window.location.href = '/sign-in?login_required=1';
                return;
            }
            if (Object.keys(selectedItems).length === 0) return;
            servicesWrap.classList.add('hidden');
            formWrap.classList.remove('hidden');
            updateTotals();
        });
    }
    updateTotals();
})();
