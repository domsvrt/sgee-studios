(function () {
    var theme = localStorage.getItem('admin-theme');
    var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    if ((theme || (prefersDark ? 'dark' : 'light')) === 'dark') {
        document.documentElement.classList.add('dark');
    }
})();

(function () {
    var feedback = document.getElementById('admin-feedback-wrap');
    var progress = document.getElementById('admin-feedback-progress');
    if (!feedback || !progress) return;
    progress.style.transition = 'width 6s linear';
    progress.style.width = '100%';
    requestAnimationFrame(function () {
        progress.style.width = '0%';
    });
    setTimeout(function () {
        feedback.remove();
    }, 6000);
})();

(function () {
    var key = 'admin-theme';
    var button = document.getElementById('theme-toggle');
    if (!button) return;
    function isDark() { return document.documentElement.classList.contains('dark'); }
    function syncLabel() { button.textContent = isDark() ? 'Light mode' : 'Dark mode'; }
    syncLabel();
    button.addEventListener('click', function () {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem(key, isDark() ? 'dark' : 'light');
        syncLabel();
    });
})();

(function () {
    function fieldsForForm(form) {
        return Array.from(document.querySelectorAll('[form="' + form.id + '"]')).filter(function (field) {
            return field.matches('input, select, textarea, button');
        });
    }

    function setRowEditing(row, editing) {
        row.classList.toggle('is-editing', editing);
        row.querySelectorAll('[data-edit-field]').forEach(function (field) {
            field.disabled = !editing;
        });
        row.querySelectorAll('[data-edit-button]').forEach(function (button) {
            button.classList.toggle('hidden', editing);
        });
        row.querySelectorAll('[data-save-button]').forEach(function (button) {
            button.classList.toggle('hidden', !editing);
        });
    }

    document.addEventListener('click', function (event) {
        var editButton = event.target.closest('[data-edit-button]');
        if (!editButton) return;
        var row = editButton.closest('tr');
        if (row) setRowEditing(row, true);
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form.id) return;
        fieldsForForm(form).forEach(function (field) {
            if (field.hasAttribute('data-edit-field')) {
                field.disabled = false;
            }
        });
    }, true);

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-create-toggle]');
        if (!toggle) return;
        var targetId = toggle.getAttribute('data-target');
        if (!targetId) return;
        var form = document.getElementById(targetId);
        if (!form) return;
        var hidden = form.classList.toggle('hidden');
        var showLabel = toggle.getAttribute('data-show-label') || 'Create';
        var hideLabel = toggle.getAttribute('data-hide-label') || 'Hide';
        toggle.textContent = hidden ? showLabel : hideLabel;
        var submit = document.querySelector('[data-create-submit="' + targetId + '"]');
        if (submit) submit.classList.toggle('hidden', hidden);
    });

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-password-toggle]');
        if (!toggle) return;
        var wrap = toggle.parentElement;
        if (!wrap) return;
        var input = wrap.querySelector('[data-password-input]');
        if (!input) return;
        var reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        toggle.textContent = reveal ? 'Hide' : 'Show';
    });

    function buildSortForm(form, table) {
        if (!form || !table) return;
        form.innerHTML = '';
        var rows = Array.from(table.querySelectorAll('tbody tr[data-sort-id]'));
        rows.forEach(function (row, index) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ordered_ids[]';
            input.value = row.getAttribute('data-sort-id');
            form.appendChild(input);
            var sortInput = row.querySelector('input[name="sort_order"]');
            if (sortInput) sortInput.value = String(index + 1);
            var sortDisplay = row.querySelector('[data-sort-order-display]');
            if (sortDisplay) sortDisplay.textContent = String(index + 1);
        });
    }

    function setSortMode(toggle, enabled) {
        var tableId = toggle.getAttribute('data-table-id');
        var table = tableId ? document.getElementById(tableId) : null;
        if (!table) return;
        var body = table.querySelector('tbody');
        if (!body) return;
        var formId = toggle.getAttribute('data-target');
        var form = formId ? document.getElementById(formId) : null;
        var saveButton = toggle.parentElement ? toggle.parentElement.querySelector('[data-sort-save]') : null;
        var search = table.closest('.admin-panel') ? table.closest('.admin-panel').querySelector('.table-tools input[type="search"]') : null;
        var pageSize = table.closest('.admin-panel') ? table.closest('.admin-panel').querySelector('.table-tools [data-page-size]') : null;

        if (enabled) {
            if (search) {
                search.value = '';
                search.dispatchEvent(new Event('input', {bubbles: true}));
            }
            if (pageSize) {
                pageSize.value = 'all';
                pageSize.dispatchEvent(new Event('change', {bubbles: true}));
            }
        }

        table.dataset.sortMode = enabled ? '1' : '';
        toggle.textContent = enabled ? 'Done sorting' : 'Sort mode';
        if (saveButton) saveButton.classList.toggle('hidden', !enabled);
        body.querySelectorAll('tr[data-sort-id]').forEach(function (row) {
            row.draggable = enabled;
            row.style.cursor = enabled ? 'grab' : '';
        });
        if (enabled && form) buildSortForm(form, table);
    }

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-sort-toggle]');
        if (!toggle) return;
        event.preventDefault();
        var tableId = toggle.getAttribute('data-table-id');
        var table = tableId ? document.getElementById(tableId) : null;
        if (!table) return;
        setSortMode(toggle, table.dataset.sortMode !== '1');
    });

    document.addEventListener('dragstart', function (event) {
        var row = event.target.closest('tr[data-sort-id]');
        if (!row) return;
        var table = row.closest('table[data-sortable-table]');
        if (!table || table.dataset.sortMode !== '1') return;
        row.classList.add('opacity-60');
        event.dataTransfer.effectAllowed = 'move';
        table.dataset.draggingId = row.getAttribute('data-sort-id') || '';
    });

    document.addEventListener('dragend', function (event) {
        var row = event.target.closest('tr[data-sort-id]');
        if (!row) return;
        row.classList.remove('opacity-60');
        row.style.cursor = 'grab';
    });

    document.addEventListener('dragover', function (event) {
        var table = event.target.closest('table[data-sortable-table]');
        if (!table || table.dataset.sortMode !== '1') return;
        var body = table.querySelector('tbody');
        if (!body) return;
        event.preventDefault();
        var dragging = table.dataset.draggingId ? body.querySelector('tr[data-sort-id="' + table.dataset.draggingId + '"]') : null;
        if (!dragging) return;
        var target = event.target.closest('tr[data-sort-id]');
        if (!target || target === dragging) return;
        var rect = target.getBoundingClientRect();
        var before = event.clientY < rect.top + (rect.height / 2);
        body.insertBefore(dragging, before ? target : target.nextSibling);
    });

    document.addEventListener('drop', function (event) {
        var table = event.target.closest('table[data-sortable-table]');
        if (!table || table.dataset.sortMode !== '1') return;
        event.preventDefault();
        var panel = table.closest('[data-sort-panel]');
        var form = panel ? panel.querySelector('[data-sort-form]') : null;
        buildSortForm(form, table);
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form.matches('[data-sort-form]')) return;
        var panel = form.closest('[data-sort-panel]');
        var table = panel ? panel.querySelector('table[data-sortable-table]') : null;
        buildSortForm(form, table);
    });

    function cellText(row, index) {
        var cell = row.children[index];
        if (!cell) return '';
        var field = cell.querySelector('input:not([type="hidden"]), select, textarea');
        if (field) {
            if (field.tagName === 'SELECT') {
                return field.options[field.selectedIndex] ? field.options[field.selectedIndex].textContent.trim() : '';
            }
            return field.value || field.textContent || '';
        }
        return cell.textContent.trim();
    }

    function initTable(table) {
        var wrapper = table.closest('.overflow-x-auto');
        var prev = wrapper ? wrapper.previousElementSibling : null;
        if (!wrapper || (prev && prev.classList && prev.classList.contains('table-tools'))) return;

        var body = table.tBodies[0];
        if (!body) return;

        var rows = Array.from(body.rows).filter(function (row) {
            return row.cells.length > 1;
        });
        if (!rows.length) return;

        var tools = document.createElement('div');
        tools.className = 'table-tools';
        tools.innerHTML = '<div class="table-tools-group"><input type="search" class="field min-w-64" placeholder="Search records"><select class="field w-auto min-w-40" data-filter><option value="">All records</option></select></div><div class="table-tools-group"><select class="field w-auto" data-page-size><option value="10">10 / page</option><option value="25">25 / page</option><option value="50">50 / page</option><option value="all">All</option></select></div>';
        wrapper.parentNode.insertBefore(tools, wrapper);

        var pager = document.createElement('div');
        pager.className = 'table-pager';
        pager.innerHTML = '<button type="button" class="btn-secondary min-h-8 px-3 py-1 text-xs" data-prev>Prev</button><span data-page-info></span><button type="button" class="btn-secondary min-h-8 px-3 py-1 text-xs" data-next>Next</button>';
        wrapper.parentNode.insertBefore(pager, wrapper.nextSibling);

        var search = tools.querySelector('input[type="search"]');
        var filter = tools.querySelector('[data-filter]');
        var pageSize = tools.querySelector('[data-page-size]');
        var prevBtn = pager.querySelector('[data-prev]');
        var nextBtn = pager.querySelector('[data-next]');
        var info = pager.querySelector('[data-page-info]');
        var page = 1;
        var sortIndex = -1;
        var sortDir = 1;

        var filterValues = Array.from(new Set(rows.map(function (row) {
            var badge = row.querySelector('.status-badge');
            return badge ? badge.textContent.trim() : '';
        }).filter(Boolean))).sort();
        filterValues.forEach(function (value) {
            var option = document.createElement('option');
            option.value = value.toLowerCase();
            option.textContent = value;
            filter.appendChild(option);
        });
        if (!filterValues.length) filter.parentElement.removeChild(filter);

        var sortableHeaders = [];
        table.querySelectorAll('thead th').forEach(function (th, index) {
            if (/actions/i.test(th.textContent)) return;
            th.tabIndex = 0;
            th.classList.add('cursor-pointer', 'select-none');
            th.setAttribute('role', 'button');
            th.setAttribute('aria-sort', 'none');
            var indicator = document.createElement('span');
            indicator.className = 'ml-1 font-bold leading-none text-slate-500 dark:text-slate-300';
            indicator.style.fontSize = '1.25rem';
            indicator.setAttribute('aria-hidden', 'true');
            indicator.textContent = '↕';
            th.appendChild(indicator);
            sortableHeaders.push({th: th, index: index, indicator: indicator});
            th.addEventListener('click', function () {
                sortDir = sortIndex === index ? sortDir * -1 : 1;
                sortIndex = index;
                page = 1;
                render();
            });
            th.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                event.preventDefault();
                th.click();
            });
        });

        function syncSortIndicators() {
            sortableHeaders.forEach(function (header) {
                var isActive = sortIndex === header.index;
                if (!isActive) {
                    header.indicator.textContent = '↕';
                    header.th.setAttribute('aria-sort', 'none');
                    return;
                }
                header.indicator.textContent = sortDir === 1 ? '↑' : '↓';
                header.th.setAttribute('aria-sort', sortDir === 1 ? 'ascending' : 'descending');
            });
        }

        function matches(row) {
            var query = search.value.trim().toLowerCase();
            var filterValue = filter ? filter.value : '';
            var text = Array.from(row.children).map(function (_cell, index) {
                return cellText(row, index);
            }).join(' ').toLowerCase();
            var badge = row.querySelector('.status-badge');
            var status = badge ? badge.textContent.trim().toLowerCase() : '';
            return (!query || text.includes(query)) && (!filterValue || status === filterValue);
        }

        function render() {
            var visible = rows.filter(matches);
            if (sortIndex >= 0) {
                visible.sort(function (a, b) {
                    return cellText(a, sortIndex).localeCompare(cellText(b, sortIndex), undefined, {numeric: true}) * sortDir;
                });
            }
            rows.forEach(function (row) { row.classList.add('hidden'); });
            visible.forEach(function (row) { body.appendChild(row); });

            var size = pageSize.value === 'all' ? visible.length || 1 : parseInt(pageSize.value, 10);
            var pages = Math.max(1, Math.ceil(visible.length / size));
            page = Math.min(page, pages);
            var start = (page - 1) * size;
            var end = start + size;
            visible.slice(start, end).forEach(function (row) { row.classList.remove('hidden'); });
            info.textContent = visible.length ? 'Page ' + page + ' of ' + pages + ' · ' + visible.length + ' records' : 'No matching records';
            prevBtn.disabled = page <= 1;
            nextBtn.disabled = page >= pages;
            syncSortIndicators();
        }

        search.addEventListener('input', function () { page = 1; render(); });
        if (filter) filter.addEventListener('change', function () { page = 1; render(); });
        pageSize.addEventListener('change', function () { page = 1; render(); });
        prevBtn.addEventListener('click', function () { page -= 1; render(); });
        nextBtn.addEventListener('click', function () { page += 1; render(); });
        render();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.admin-table').forEach(initTable);
    });
})();
