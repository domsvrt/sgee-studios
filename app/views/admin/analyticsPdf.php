<?php
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$range = (string) ($range ?? 'monthly');
$generatedAt = (string) ($generatedAt ?? date('F j, Y g:i A'));
$metrics = $metrics ?? [];
$healthStats = $healthStats ?? [];
$bookingStatusCounts = $bookingStatusCounts ?? [];
$monthlyRevenue = $monthlyRevenue ?? [];
$topServices = $topServices ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGee Studios Analytics Report</title>
    <style>
        @page { size: A4; margin: 16mm; }
        :root { color-scheme: light; }
        body {
            font-family: "Times New Roman", Georgia, serif;
            color: #0f172a;
            background: #fff;
            margin: 0;
            font-size: 12px;
            line-height: 1.45;
        }
        .toolbar {
            position: fixed;
            right: 12px;
            top: 12px;
            display: flex;
            gap: 8px;
        }
        .toolbar button {
            border: 1px solid #0f172a;
            background: #0f172a;
            color: #fff;
            border-radius: 5px;
            padding: 6px 10px;
            font-size: 11px;
            cursor: pointer;
        }
        .sheet {
            max-width: 760px;
            margin: 0 auto;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .title { margin: 0; font-size: 24px; letter-spacing: .04em; }
        .subtitle { margin: 4px 0 0; color: #334155; font-size: 12px; }
        .meta { margin-top: 8px; color: #475569; font-size: 11px; }
        .section { margin-top: 16px; }
        .section h2 {
            margin: 0 0 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .kpi {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px;
            min-height: 72px;
        }
        .kpi .label { color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; }
        .kpi .value { margin-top: 4px; font-size: 18px; font-weight: 700; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f1f5f9;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .muted { color: #64748b; }
        .footer {
            margin-top: 18px;
            color: #64748b;
            font-size: 10px;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
        }
        @media print {
            .toolbar { display: none; }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button type="button" onclick="window.print()">Print / Save PDF</button>
</div>
<main class="sheet">
    <header class="header">
        <h1 class="title">SGee Studios Analytics Report</h1>
        <p class="subtitle">Formal operational and performance report</p>
        <p class="meta">Period: <?= $e(ucfirst($range)) ?> | Generated: <?= $e($generatedAt) ?></p>
    </header>

    <section class="section">
        <h2>Executive Summary</h2>
        <div class="kpi-grid">
            <article class="kpi"><div class="label">Total Users</div><div class="value"><?= $e((string) ((int) ($metrics['totalUsers'] ?? 0))) ?></div></article>
            <article class="kpi"><div class="label">Total Bookings</div><div class="value"><?= $e((string) ((int) ($metrics['totalBookings'] ?? 0))) ?></div></article>
            <article class="kpi"><div class="label">Total Revenue</div><div class="value">PHP <?= $e(number_format((float) ($metrics['totalRevenue'] ?? 0), 2)) ?></div></article>
            <article class="kpi"><div class="label">Completion Rate</div><div class="value"><?= $e(number_format((float) ($healthStats['completionRate'] ?? 0), 1)) ?>%</div></article>
            <article class="kpi"><div class="label">Cancellation Rate</div><div class="value"><?= $e(number_format((float) ($healthStats['cancellationRate'] ?? 0), 1)) ?>%</div></article>
            <article class="kpi"><div class="label">Avg Booking Value</div><div class="value">PHP <?= $e(number_format((float) ($healthStats['avgBookingValue'] ?? 0), 2)) ?></div></article>
        </div>
    </section>

    <section class="section">
        <h2>Booking Status Distribution</h2>
        <table>
            <thead><tr><th>Status</th><th>Total</th></tr></thead>
            <tbody>
            <?php foreach ($bookingStatusCounts as $status => $count): ?>
                <tr><td><?= $e(ucfirst((string) $status)) ?></td><td><?= $e((string) ((int) $count)) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$bookingStatusCounts): ?>
                <tr><td colspan="2" class="muted">No booking status records for this period.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2>Revenue Trend</h2>
        <table>
            <thead><tr><th>Period</th><th>Revenue (PHP)</th></tr></thead>
            <tbody>
            <?php foreach ($monthlyRevenue as $period => $revenue): ?>
                <tr><td><?= $e((string) $period) ?></td><td><?= $e(number_format((float) $revenue, 2)) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$monthlyRevenue): ?>
                <tr><td colspan="2" class="muted">No revenue trend data for this period.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2>Top Services</h2>
        <table>
            <thead><tr><th>Service</th><th>Bookings</th><th>Revenue (PHP)</th></tr></thead>
            <tbody>
            <?php foreach ($topServices as $name => $stat): ?>
                <tr>
                    <td><?= $e((string) $name) ?></td>
                    <td><?= $e((string) ((int) ($stat['count'] ?? 0))) ?></td>
                    <td><?= $e(number_format((float) ($stat['revenue'] ?? 0), 2)) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$topServices): ?>
                <tr><td colspan="3" class="muted">No top-service data for this period.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

    <p class="footer">Prepared by SGee Studios Admin System. This document is intended for internal operations reporting.</p>
</main>
</body>
</html>
