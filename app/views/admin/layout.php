<?php
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
/** @var array{type?: string, message?: string}|null $flash */
$flash = $flash ?? null;
/** @var string $content */
$content = $content ?? '';
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($title ?? 'Admin') ?> | SGee Studios</title>
    <script>
        (function () {
            var theme = localStorage.getItem('admin-theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            if ((theme || (prefersDark ? 'dark' : 'light')) === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="h-full admin-shell antialiased">
<?php
$nav = [
    'dashboard' => ['/admin', 'Dashboard'],
    'users' => ['/admin/users', 'Users'],
    'categories' => ['/admin/categories', 'Categories'],
    'services' => ['/admin/services', 'Services'],
    'bookings' => ['/admin/bookings', 'Bookings'],
    'logs' => ['/admin/logs', 'Status Logs'],
];

$navIcon = static function (string $key): string {
    $attrs = 'class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
    $icons = [
        'dashboard' => '<svg ' . $attrs . '><path d="M3 13h8V3H3v10Z"></path><path d="M13 21h8V11h-8v10Z"></path><path d="M13 3v6h8V3h-8Z"></path><path d="M3 21h8v-6H3v6Z"></path></svg>',
        'users' => '<svg ' . $attrs . '><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'categories' => '<svg ' . $attrs . '><path d="M4 4h6v6H4V4Z"></path><path d="M14 4h6v6h-6V4Z"></path><path d="M4 14h6v6H4v-6Z"></path><path d="M14 14h6v6h-6v-6Z"></path></svg>',
        'services' => '<svg ' . $attrs . '><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.1-3.1a5 5 0 0 1-6.6 6.6L7.6 19.4a2.1 2.1 0 0 1-3-3l6.6-6.6a5 5 0 0 1 6.6-6.6l-3.1 3.1Z"></path></svg>',
        'bookings' => '<svg ' . $attrs . '><path d="M8 2v4"></path><path d="M16 2v4"></path><path d="M3 10h18"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="m9 16 2 2 4-4"></path></svg>',
        'logs' => '<svg ' . $attrs . '><path d="M12 8v5l3 2"></path><circle cx="12" cy="12" r="10"></circle><path d="M5 5 3 3"></path><path d="m19 5 2-2"></path></svg>',
    ];

    return $icons[$key] ?? '';
};
?>
<div class="min-h-screen lg:flex">
    <aside class="admin-sidebar flex flex-col">
        <div class="admin-brand">
            <div class="admin-logo">SG</div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-300">SGee Studios</p>
                <h1 class="truncate text-sm font-semibold text-slate-950 dark:text-white">Operations Admin</h1>
            </div>
        </div>
        <nav class="mt-4 grid gap-1">
            <?php foreach ($nav as $key => [$href, $label]): ?>
                <a href="<?= $e($href) ?>" class="group admin-nav-link <?= ($activeNav ?? '') === $key ? 'admin-nav-link-active' : 'admin-nav-link-idle' ?>">
                    <span class="admin-nav-mark <?= ($activeNav ?? '') === $key ? 'admin-nav-mark-active' : 'admin-nav-mark-idle' ?>"><?= $navIcon($key) ?></span>
                    <span class="truncate"><?= $e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <main class="min-w-0 flex-1">
        <header class="admin-topbar">
            <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-300">Admin Workspace</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950 dark:text-white"><?= $e($title ?? 'Admin') ?></h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button id="theme-toggle" type="button" class="btn-secondary">Dark mode</button>
                    <button id="logout-toggle-btn" type="button" class="btn-primary" onclick="var p=document.getElementById('logout-confirm-inline');var b=document.getElementById('logout-toggle-btn');if(p&&b){p.classList.remove('hidden');b.classList.add('hidden');}">Logout</button>
                    <div id="logout-confirm-inline" class="hidden flex items-center gap-2">
                        <form method="post" action="/logout">
                            <button class="btn-primary" style="background-color:#dc2626;border-color:#dc2626;">Yes, logout</button>
                        </form>
                        <button type="button" class="btn-secondary" onclick="var p=document.getElementById('logout-confirm-inline');var b=document.getElementById('logout-toggle-btn');if(p&&b){p.classList.add('hidden');b.classList.remove('hidden');}">Cancel</button>
                    </div>
                </div>
            </div>
        </header>
        <?php if ($flash): ?>
            <div id="admin-feedback-wrap" class="mt-5 w-full px-4 md:px-8">
                <div id="admin-feedback" class="overflow-hidden rounded-lg border pt-0 shadow-sm <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300' : 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300' ?>">
                    <div class="flex items-start justify-between gap-2 px-3 py-2 text-sm font-semibold">
                        <p><?= $e($flash['message']) ?></p>
                        <button type="button" class="text-xs font-black opacity-70 hover:opacity-100" onclick="var f=document.getElementById('admin-feedback-wrap');if(f){f.remove();}">✕</button>
                    </div>
                    <div class="h-1 w-full bg-black/10 dark:bg-white/20">
                        <div id="admin-feedback-progress" class="h-full bg-current opacity-70"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <section class="w-full p-4 md:p-8">
            <?= $content ?>
        </section>
    </main>
</div>
<script>
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
</script>
<script>
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
</script>
<script>
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
            var prev = pager.querySelector('[data-prev]');
            var next = pager.querySelector('[data-next]');
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
                prev.disabled = page <= 1;
                next.disabled = page >= pages;
                syncSortIndicators();
            }

            search.addEventListener('input', function () { page = 1; render(); });
            if (filter) filter.addEventListener('change', function () { page = 1; render(); });
            pageSize.addEventListener('change', function () { page = 1; render(); });
            prev.addEventListener('click', function () { page -= 1; render(); });
            next.addEventListener('click', function () { page += 1; render(); });
            render();
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.admin-table').forEach(initTable);
        });
    })();
</script>
</body>
</html>
