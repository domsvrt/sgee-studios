<?php
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
/** @var array<string, mixed> $metrics */
$metrics = $metrics ?? [];
/** @var array<string, int> $userRoleCounts */
$userRoleCounts = $userRoleCounts ?? [];
/** @var array<string, int> $bookingStatusCounts */
$bookingStatusCounts = $bookingStatusCounts ?? [];
/** @var array<string, float> $monthlyRevenue */
$monthlyRevenue = $monthlyRevenue ?? [];
/** @var array<string, array{count:int,revenue:float}> $topServices */
$topServices = $topServices ?? [];
/** @var array<string, mixed> $healthStats */
$healthStats = $healthStats ?? [];
$range = (string) ($range ?? 'monthly');
$rangeOptions = $rangeOptions ?? ['weekly', 'monthly', 'yearly'];

$monthlyRevenueChart = [];
foreach ($monthlyRevenue as $month => $revenue) {
    $monthlyRevenueChart[] = ['label' => $month, 'value' => (float) $revenue];
}
$bookingStatusChart = [];
foreach ($bookingStatusCounts as $status => $total) {
    $bookingStatusChart[] = ['label' => ucfirst($status), 'value' => (int) $total];
}
$userRoleChart = [];
foreach ($userRoleCounts as $role => $total) {
    $userRoleChart[] = ['label' => ucfirst($role), 'value' => (int) $total];
}
$topServicesChart = [];
foreach ($topServices as $name => $stat) {
    $topServicesChart[] = ['label' => $name, 'value' => (int) ($stat['count'] ?? 0)];
}

$kpiCharts = [
    ['title' => 'Total Users', 'value' => (int) ($metrics['totalUsers'] ?? 0), 'color' => '#14b8a6'],
    ['title' => 'Total Bookings', 'value' => (int) ($metrics['totalBookings'] ?? 0), 'color' => '#f59e0b'],
];

$healthKpis = [
    ['title' => 'Completion Rate', 'value' => number_format((float) ($healthStats['completionRate'] ?? 0), 1) . '%', 'hint' => 'Completed / Total bookings'],
    ['title' => 'Cancellation Rate', 'value' => number_format((float) ($healthStats['cancellationRate'] ?? 0), 1) . '%', 'hint' => 'Cancelled / Total bookings'],
    ['title' => 'Avg Booking Value', 'value' => 'PHP ' . number_format((float) ($healthStats['avgBookingValue'] ?? 0), 2), 'hint' => 'Revenue per booking'],
    ['title' => 'Upcoming Load', 'value' => (string) ((int) ($healthStats['upcomingLoad'] ?? 0)), 'hint' => 'Pending + Confirmed'],
    ['title' => 'Monthly Booking Trend', 'value' => ((int) ($healthStats['monthlyBookingTrend'] ?? 0) >= 0 ? '+' : '') . (string) ((int) ($healthStats['monthlyBookingTrend'] ?? 0)), 'hint' => 'This month vs last month'],
];
$asOfDate = date('F j, Y');
?>

<style>
.analytics-grid { display:grid; gap:1rem; }
.analytics-kpis { grid-template-columns: repeat(1, minmax(0,1fr)); }
.analytics-panels { grid-template-columns: repeat(1, minmax(0,1fr)); }
@media (min-width: 768px){
  .analytics-kpis { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
@media (min-width: 1280px){
  .analytics-kpis { grid-template-columns: repeat(2, minmax(0,1fr)); }
  .analytics-panels { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
.analytics-card {
  border: 1px solid rgba(148,163,184,.3);
  border-radius: 14px;
  background: linear-gradient(160deg, rgba(255,255,255,.82), rgba(241,245,249,.88));
  box-shadow: 0 10px 30px rgba(15,23,42,.08);
}
.dark .analytics-card {
  border-color: rgba(71,85,105,.55);
  background: linear-gradient(160deg, rgba(15,23,42,.75), rgba(30,41,59,.78));
  box-shadow: 0 14px 34px rgba(2,6,23,.45);
}
.health-card {
  position: relative;
  overflow: hidden;
  border-radius: 14px;
  border: 1px solid rgba(148,163,184,.28);
  background: linear-gradient(160deg, rgba(255,255,255,.9), rgba(248,250,252,.85));
  box-shadow: 0 10px 22px rgba(15,23,42,.08);
}
.health-card::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, var(--accent-from), var(--accent-to));
}
.dark .health-card {
  border-color: rgba(71,85,105,.55);
  background: linear-gradient(160deg, rgba(15,23,42,.84), rgba(30,41,59,.72));
  box-shadow: 0 16px 32px rgba(2,6,23,.45);
}
</style>

<section class="admin-panel mb-5">
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Analytics Range</h3>
                <p class="admin-panel-subtitle">Data window set to <?= $e(ucfirst($range)) ?> as of <?= $e($asOfDate) ?></p>
            </div>
            <form method="get" action="/admin/analytics" class="flex items-center gap-2">
                <select name="range" class="field field-sm min-w-40">
                    <?php foreach ($rangeOptions as $option): ?>
                        <option value="<?= $e($option) ?>" <?= $range === $option ? 'selected' : '' ?>><?= $e(ucfirst($option)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-primary">Apply</button>
                <a href="/admin/analytics/export-pdf?range=<?= $e(urlencode($range)) ?>" target="_blank" rel="noopener noreferrer" class="btn-secondary">Export PDF</a>
            </form>
        </div>
    </div>
</section>

<div class="analytics-grid analytics-kpis">
    <?php foreach ($kpiCharts as $index => $kpi): ?>
        <section class="analytics-card p-4" title="As of <?= $e($asOfDate) ?>">
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400"><?= $e($kpi['title']) ?></p>
            <p class="mt-2 text-4xl font-black text-slate-950 dark:text-white"><?= $e($kpi['value']) ?></p>
            <canvas class="mt-3 w-full" height="72" data-chart="spark" data-color="<?= $e($kpi['color']) ?>" data-series='<?= $e(json_encode([
                ['label' => 'now', 'value' => (int) $kpi['value']],
                ['label' => '2', 'value' => max(1, (int) round((int) $kpi['value'] * 0.88))],
                ['label' => '3', 'value' => max(1, (int) round((int) $kpi['value'] * 0.91))],
                ['label' => '4', 'value' => max(1, (int) round((int) $kpi['value'] * 0.95))],
                ['label' => '5', 'value' => max(1, (int) round((int) $kpi['value'] * 1.0))],
            ], JSON_UNESCAPED_SLASHES)) ?>'></canvas>
        </section>
    <?php endforeach; ?>
</div>

<div class="mt-5 analytics-grid" style="grid-template-columns: repeat(1, minmax(0,1fr));">
    <section class="analytics-card p-5">
        <h3 class="text-lg font-black text-slate-900 dark:text-white">Health Snapshot</h3>
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Important monitoring stats for operations</p>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            <?php foreach ($healthKpis as $index => $item): ?>
                <?php
                $accents = [
                    ['#14b8a6', '#2dd4bf'],
                    ['#ef4444', '#fb7185'],
                    ['#8b5cf6', '#a78bfa'],
                    ['#f59e0b', '#fbbf24'],
                    ['#0ea5e9', '#38bdf8'],
                ];
                [$from, $to] = $accents[$index % count($accents)];
                ?>
                <div class="health-card p-4" style="--accent-from: <?= $e($from) ?>; --accent-to: <?= $e($to) ?>;">
                    <p class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400"><?= $e($item['title']) ?></p>
                    <p class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white"><?= $e($item['value']) ?></p>
                    <p class="mt-2 text-[11px] leading-5 text-slate-500 dark:text-slate-400"><?= $e($item['hint']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<div class="mt-5 analytics-grid analytics-panels">
    <section class="analytics-card p-5">
        <h3 class="text-lg font-black text-slate-900 dark:text-white">Booking Status</h3>
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Bar chart distribution</p>
        <canvas class="mt-4 w-full" height="280" data-chart="bar" data-tooltip="status-name" data-color="#14b8a6" data-series='<?= $e(json_encode($bookingStatusChart, JSON_UNESCAPED_SLASHES)) ?>'></canvas>
    </section>

    <section class="analytics-card p-5">
        <h3 class="text-lg font-black text-slate-900 dark:text-white">Top Booked Services</h3>
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Bar chart by booked quantity</p>
        <canvas class="mt-4 w-full" height="280" data-chart="bar" data-tooltip="service-name" data-color="#f43f5e" data-series='<?= $e(json_encode($topServicesChart, JSON_UNESCAPED_SLASHES)) ?>'></canvas>
    </section>

    <section class="analytics-card p-5 xl:col-span-2">
        <h3 class="text-lg font-black text-slate-900 dark:text-white">Revenue Trend</h3>
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Line chart by booking month</p>
        <canvas class="mt-4 w-full" height="360" data-chart="line" data-color="#8b5cf6" data-series='<?= $e(json_encode($monthlyRevenueChart, JSON_UNESCAPED_SLASHES)) ?>'></canvas>
    </section>

</div>

<script>
(function () {
    function ensureChartTooltip() {
        var tooltip = document.getElementById('analytics-chart-tooltip');
        if (tooltip) return tooltip;
        tooltip = document.createElement('div');
        tooltip.id = 'analytics-chart-tooltip';
        tooltip.style.position = 'fixed';
        tooltip.style.zIndex = '2500';
        tooltip.style.pointerEvents = 'none';
        tooltip.style.padding = '6px 8px';
        tooltip.style.borderRadius = '8px';
        tooltip.style.fontSize = '11px';
        tooltip.style.fontWeight = '700';
        tooltip.style.background = 'rgba(15,23,42,0.92)';
        tooltip.style.color = '#fff';
        tooltip.style.boxShadow = '0 8px 24px rgba(2,6,23,0.35)';
        tooltip.style.opacity = '0';
        tooltip.style.transition = 'opacity 120ms ease';
        document.body.appendChild(tooltip);
        return tooltip;
    }

    function parseSeries(el) { try { return JSON.parse(el.getAttribute('data-series') || '[]'); } catch (e) { return []; } }
    function parseColors(el, fallback) { try { var c = JSON.parse(el.getAttribute('data-colors') || '[]'); return c.length ? c : fallback; } catch (e) { return fallback; } }
    function getCanvas(el) { var ctx = el.getContext('2d'); if (!ctx) return null; el.width = Math.max(320, Math.floor(el.clientWidth)); return ctx; }

    function drawAxes(ctx, w, h, pad) {
        ctx.strokeStyle = '#94a3b8';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(pad.l, pad.t);
        ctx.lineTo(pad.l, h - pad.b);
        ctx.lineTo(w - pad.r, h - pad.b);
        ctx.stroke();
    }

    function drawBar(el, series, color) {
        var ctx = getCanvas(el); if (!ctx || !series.length) return;
        var w = el.width, h = el.height;
        var pad = { t: 18, r: 12, b: 56, l: 36 };
        var max = Math.max.apply(null, series.map(function (s) { return Number(s.value) || 0; })) || 1;
        var plotW = w - pad.l - pad.r, plotH = h - pad.t - pad.b;
        var slot = plotW / series.length, barW = Math.max(8, slot * 0.62);
        var bars = [];
        ctx.clearRect(0, 0, w, h); drawAxes(ctx, w, h, pad);
        ctx.font = '11px sans-serif'; ctx.textAlign = 'center';
        series.forEach(function (p, i) {
            var v = Number(p.value) || 0;
            var bh = (v / max) * plotH;
            var x = pad.l + i * slot + (slot - barW) / 2;
            var y = h - pad.b - bh;
            ctx.fillStyle = color; ctx.fillRect(x, y, barW, bh);
            bars.push({x: x, y: y, w: barW, h: bh, label: String(p.label || ''), value: v});
            ctx.fillStyle = '#475569'; ctx.fillText(String(v), x + barW / 2, y - 6);
            var label = String(p.label || ''); if (label.length > 11) label = label.slice(0, 11) + '...';
            ctx.fillText(label, x + barW / 2, h - pad.b + 14);
        });

        if (el.getAttribute('data-tooltip') === 'service-name') {
            el._barPoints = bars;
            if (!el._tooltipBound) {
                var tooltip = ensureChartTooltip();
                el.addEventListener('mousemove', function (event) {
                    var rect = el.getBoundingClientRect();
                    var x = event.clientX - rect.left;
                    var y = event.clientY - rect.top;
                    var hit = null;
                    (el._barPoints || []).forEach(function (bar) {
                        if (x >= bar.x && x <= bar.x + bar.w && y >= bar.y && y <= bar.y + bar.h) {
                            hit = bar;
                        }
                    });
                    if (!hit) {
                        tooltip.style.opacity = '0';
                        return;
                    }
                    tooltip.textContent = hit.label;
                    tooltip.style.left = (event.clientX + 12) + 'px';
                    tooltip.style.top = (event.clientY - 28) + 'px';
                    tooltip.style.opacity = '1';
                });
                el.addEventListener('mouseleave', function () {
                    tooltip.style.opacity = '0';
                });
                el._tooltipBound = true;
            }
        }
    }

    function drawHBar(el, series, color) {
        var ctx = getCanvas(el); if (!ctx || !series.length) return;
        var w = el.width, h = el.height;
        var pad = { t: 14, r: 18, b: 14, l: 140 };
        var max = Math.max.apply(null, series.map(function (s) { return Number(s.value) || 0; })) || 1;
        var plotW = w - pad.l - pad.r, plotH = h - pad.t - pad.b;
        var row = plotH / series.length, barH = Math.max(8, row * 0.55);
        ctx.clearRect(0, 0, w, h);
        ctx.font = '11px sans-serif';
        series.forEach(function (p, i) {
            var v = Number(p.value) || 0;
            var bw = (v / max) * plotW;
            var y = pad.t + i * row + (row - barH) / 2;
            ctx.fillStyle = '#e2e8f0'; ctx.fillRect(pad.l, y, plotW, barH);
            ctx.fillStyle = color; ctx.fillRect(pad.l, y, bw, barH);
            ctx.fillStyle = '#475569';
            var label = String(p.label || ''); if (label.length > 20) label = label.slice(0, 20) + '...';
            ctx.textAlign = 'right'; ctx.fillText(label, pad.l - 8, y + barH - 2);
            ctx.textAlign = 'left'; ctx.fillText(String(v), pad.l + bw + 6, y + barH - 2);
        });
    }

    function drawLine(el, series, color) {
        var ctx = getCanvas(el); if (!ctx || !series.length) return;
        var w = el.width, h = el.height;
        var pad = { t: 20, r: 12, b: 48, l: 46 };
        var max = Math.max.apply(null, series.map(function (s) { return Number(s.value) || 0; })) || 1;
        var plotW = w - pad.l - pad.r, plotH = h - pad.t - pad.b;
        ctx.clearRect(0, 0, w, h); drawAxes(ctx, w, h, pad);
        ctx.strokeStyle = color; ctx.lineWidth = 2.5; ctx.beginPath();
        series.forEach(function (p, i) {
            var x = pad.l + (series.length === 1 ? plotW / 2 : i / (series.length - 1) * plotW);
            var y = h - pad.b - ((Number(p.value) || 0) / max * plotH);
            if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
        });
        ctx.stroke();
        ctx.fillStyle = color; ctx.font = '11px sans-serif'; ctx.textAlign = 'center';
        series.forEach(function (p, i) {
            var x = pad.l + (series.length === 1 ? plotW / 2 : i / (series.length - 1) * plotW);
            var y = h - pad.b - ((Number(p.value) || 0) / max * plotH);
            ctx.beginPath(); ctx.arc(x, y, 3.5, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#475569';
            ctx.fillText(String(p.label || ''), x, h - pad.b + 14);
            ctx.fillText(String((Number(p.value) || 0).toFixed(0)), x, y - 8);
            ctx.fillStyle = color;
        });
    }

    function drawDonut(el, series, colors) {
        var ctx = getCanvas(el); if (!ctx || !series.length) return;
        var w = el.width, h = el.height;
        ctx.clearRect(0, 0, w, h);
        var total = series.reduce(function (sum, p) { return sum + (Number(p.value) || 0); }, 0) || 1;
        var cx = Math.floor(w * 0.35), cy = Math.floor(h * 0.5);
        var outer = Math.min(w, h) * 0.28, inner = outer * 0.58;
        var start = -Math.PI / 2;
        series.forEach(function (p, i) {
            var val = Number(p.value) || 0;
            var arc = (val / total) * Math.PI * 2;
            ctx.beginPath();
            ctx.arc(cx, cy, outer, start, start + arc);
            ctx.arc(cx, cy, inner, start + arc, start, true);
            ctx.closePath();
            ctx.fillStyle = colors[i % colors.length];
            ctx.fill();
            start += arc;
        });
        ctx.fillStyle = '#0f172a'; ctx.font = 'bold 18px sans-serif'; ctx.textAlign = 'center';
        ctx.fillText(String(total), cx, cy + 6);
        ctx.fillStyle = '#475569'; ctx.font = '11px sans-serif'; ctx.fillText('TOTAL', cx, cy + 22);

        var lx = Math.floor(w * 0.62), ly = Math.floor(h * 0.24), step = 22;
        ctx.textAlign = 'left'; ctx.font = '11px sans-serif';
        series.forEach(function (p, i) {
            ctx.fillStyle = colors[i % colors.length]; ctx.fillRect(lx, ly + i * step - 8, 10, 10);
            ctx.fillStyle = '#475569'; ctx.fillText(String(p.label) + ' (' + String(p.value) + ')', lx + 16, ly + i * step);
        });
    }

    function drawSpark(el, series, color) {
        var ctx = getCanvas(el); if (!ctx || !series.length) return;
        var w = el.width, h = el.height;
        var pad = { t: 8, r: 8, b: 8, l: 8 };
        var max = Math.max.apply(null, series.map(function (s) { return Number(s.value) || 0; })) || 1;
        var plotW = w - pad.l - pad.r, plotH = h - pad.t - pad.b;
        ctx.clearRect(0, 0, w, h);
        ctx.strokeStyle = color; ctx.lineWidth = 2; ctx.beginPath();
        series.forEach(function (p, i) {
            var x = pad.l + (series.length === 1 ? plotW / 2 : i / (series.length - 1) * plotW);
            var y = h - pad.b - ((Number(p.value) || 0) / max * plotH);
            if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
        });
        ctx.stroke();
    }

    function renderAllCharts() {
        document.querySelectorAll('canvas[data-chart]').forEach(function (el) {
            var type = el.getAttribute('data-chart') || 'bar';
            var color = el.getAttribute('data-color') || '#0ea5e9';
            var series = parseSeries(el);
            if (!series.length) return;
            if (type === 'line') return drawLine(el, series, color);
            if (type === 'donut') return drawDonut(el, series, parseColors(el, ['#0ea5e9', '#22c55e', '#f59e0b']));
            if (type === 'hbar') return drawHBar(el, series, color);
            if (type === 'spark') return drawSpark(el, series, color);
            return drawBar(el, series, color);
        });
    }

    window.addEventListener('resize', renderAllCharts);
    renderAllCharts();
})();
</script>
