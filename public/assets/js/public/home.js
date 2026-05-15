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
    var selectedCategoryId = 0;
    var selectedItems = {};

    function getCategory() {
        return catalog.find(function (row) { return Number(row.id) === Number(selectedCategoryId); }) || null;
    }

    function updateTotals() {
        var keys = Object.keys(selectedItems);
        var total = 0;
        keys.forEach(function (key) {
            var row = selectedItems[key];
            total += Number(row.price || 0) * Number(row.qty || 1);
        });
        selectedCount.textContent = keys.length + ' item' + (keys.length === 1 ? '' : 's') + ' selected';
        totalEl.textContent = '$' + total.toFixed(2);
        summaryEl.textContent = keys.length > 0
            ? 'Selected services: ' + keys.map(function (key) { return selectedItems[key].name + ' (x' + selectedItems[key].qty + ')'; }).join(' | ')
            : 'No services selected yet.';
    }

    function setSelection(item, mode, sectionCode) {
        if (mode === 'single') {
            Object.keys(selectedItems).forEach(function (key) {
                if (selectedItems[key].sectionCode === sectionCode) {
                    delete selectedItems[key];
                }
            });
            selectedItems[item.code] = { name: item.name, price: Number(item.price || 0), qty: 1, sectionCode: sectionCode };
            return;
        }

        if (selectedItems[item.code]) {
            delete selectedItems[item.code];
        } else {
            selectedItems[item.code] = { name: item.name, price: Number(item.price || 0), qty: 1, sectionCode: sectionCode };
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
                items += '</div><div class="text-right"><p class="text-lg font-black text-slate-950">$' + Number(item.price || 0).toFixed(2) + '</p>';
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
            servicesWrap.classList.add('hidden');
            formWrap.classList.remove('hidden');
            updateTotals();
        });
    }
    updateTotals();
})();
