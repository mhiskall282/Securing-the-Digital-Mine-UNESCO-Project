<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Report — Npontu Technologies</title>
    <style>
        /* ── Reset & Base ────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.45;
        }

        /* ── Page Layout ──────────────────────────────────── */
        .page {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 28px 32px;
        }

        /* ── Cover Header ─────────────────────────────────── */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #1B6B3A;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .brand-block { display: flex; align-items: center; gap: 12px; }

        .brand-logo {
            width: 42px; height: 42px;
            background: #1B6B3A;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #F5C518;
            font-size: 18px; font-weight: 900;
            flex-shrink: 0;
        }

        .brand-name { font-size: 15pt; font-weight: 800; color: #1B6B3A; letter-spacing: -0.3px; }
        .brand-tagline { font-size: 7.5pt; color: #6b7280; margin-top: 1px; }

        .report-meta { text-align: right; }
        .report-meta .report-title {
            font-size: 13pt; font-weight: 700;
            color: #111827;
        }
        .report-meta .report-subtitle { font-size: 8.5pt; color: #6b7280; margin-top: 3px; }

        /* ── Criteria Strip ───────────────────────────────── */
        .criteria-strip {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px 24px;
            margin-bottom: 16px;
            font-size: 8.5pt;
        }
        .criteria-strip .crit { display: flex; align-items: center; gap: 5px; }
        .criteria-strip .crit-label { font-weight: 600; color: #374151; }
        .criteria-strip .crit-value { color: #1B6B3A; font-weight: 700; }

        /* ── Summary KPIs ─────────────────────────────────── */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .kpi-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }
        .kpi-card.kpi-total  { border-color: #d1d5db; }
        .kpi-card.kpi-done   { border-color: #86efac; background: #f0fdf4; }
        .kpi-card.kpi-pending{ border-color: #fde68a; background: #fffbeb; }
        .kpi-card.kpi-pct    { border-color: #93c5fd; background: #eff6ff; }

        .kpi-number { font-size: 22pt; font-weight: 900; line-height: 1; }
        .kpi-card.kpi-total   .kpi-number { color: #111827; }
        .kpi-card.kpi-done    .kpi-number { color: #1B6B3A; }
        .kpi-card.kpi-pending .kpi-number { color: #d97706; }
        .kpi-card.kpi-pct     .kpi-number { color: #2563eb; }

        .kpi-label { font-size: 7.5pt; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; margin-top: 3px; }

        /* ── Progress Bar ─────────────────────────────────── */
        .progress-wrap { margin-bottom: 20px; }
        .progress-label { font-size: 8pt; font-weight: 600; color: #374151; margin-bottom: 4px;
            display: flex; justify-content: space-between; }
        .progress-bar-bg { height: 10px; background: #fde68a; border-radius: 99px; overflow: hidden; }
        .progress-bar-fill { height: 100%; background: #1B6B3A; border-radius: 99px; transition: width 0.3s; }

        /* ── Date Section ─────────────────────────────────── */
        .date-section { margin-bottom: 20px; page-break-inside: avoid; }

        .date-header {
            background: #1B6B3A;
            color: #fff;
            font-weight: 700;
            font-size: 9.5pt;
            padding: 6px 12px;
            border-radius: 5px 5px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .date-header .date-count {
            background: rgba(255,255,255,0.2);
            font-size: 8pt;
            padding: 1px 8px;
            border-radius: 99px;
        }

        /* ── Table ────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        thead th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 700;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        tbody td {
            padding: 6px 8px;
            border: 1px solid #f0f0f0;
            vertical-align: top;
            color: #374151;
        }

        tbody tr:nth-child(even) td { background: #fafafa; }

        tbody tr:hover td { background: #f0fdf4; }

        .status-done {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            font-weight: 700;
            font-size: 7pt;
            padding: 1px 7px;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .status-pending {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            font-weight: 700;
            font-size: 7pt;
            padding: 1px 7px;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .remark-cell { font-style: italic; color: #6b7280; font-size: 8pt; }
        .no-remark   { color: #d1d5db; font-size: 8pt; }

        /* row# column */
        .col-no { width: 32px; color: #9ca3af; font-size: 7.5pt; text-align: center; }

        /* ── Date section sub-totals ─────────────────────── */
        .section-footer td {
            background: #f9fafb;
            font-weight: 600;
            font-size: 8pt;
            color: #374151;
            border-top: 2px solid #e5e7eb;
        }

        /* ── Grand Total Row ──────────────────────────────── */
        .grand-total td {
            background: #1B6B3A;
            color: #fff;
            font-weight: 700;
            font-size: 9pt;
            padding: 8px 12px;
        }

        /* ── Footer ───────────────────────────────────────── */
        .report-footer {
            margin-top: 24px;
            border-top: 2px solid #1B6B3A;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5pt;
            color: #9ca3af;
        }
        .report-footer strong { color: #374151; }

        /* ── Print Specific ───────────────────────────────── */
        @media print {
            body { font-size: 9pt; }
            .no-print { display: none !important; }
            .page { padding: 0; max-width: 100%; }
            .date-section { page-break-inside: avoid; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }

            @page {
                size: A4 landscape;
                margin: 12mm 10mm;
            }
        }

        @media screen {
            body { background: #e5e7eb; padding: 20px; }
            .page {
                background: #fff;
                box-shadow: 0 4px 24px rgba(0,0,0,0.12);
                border-radius: 8px;
            }
            .no-print button {
                background: #1B6B3A; color: #fff;
                border: none; padding: 10px 24px;
                border-radius: 6px; font-size: 11pt;
                font-weight: 600; cursor: pointer;
                margin-right: 8px;
                transition: background 0.2s;
            }
            .no-print button:hover { background: #12492a; }
            .no-print .btn-cancel {
                background: #f3f4f6; color: #374151;
            }
        }
    </style>
</head>
<body>

<div class="page">

    {{-- ── Print Controls (screen only) ─────────────────────────────── --}}
    <div class="no-print" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <button onclick="window.print()">🖨 Save as PDF / Print</button>
        <button class="btn-cancel" onclick="window.close()">✕ Close</button>
        <span style="font-size: 9pt; color: #6b7280; margin-left: 8px;">
            Preview — {{ $total }} record(s) will be included across all pages
        </span>
    </div>

    {{-- ── Report Header ───────────────────────────────────────────── --}}
    <div class="report-header">
        <div class="brand-block">
            <div class="brand-logo">N</div>
            <div>
                <div class="brand-name">Npontu Technologies</div>
                <div class="brand-tagline">Support Activity Tracker — Official Report</div>
            </div>
        </div>
        <div class="report-meta">
            <div class="report-title">Activity Report</div>
            <div class="report-subtitle">
                Generated: {{ $generatedAt }} &nbsp;|&nbsp; By: {{ $generatedBy }}
            </div>
        </div>
    </div>

    {{-- ── Criteria Strip ──────────────────────────────────────────── --}}
    <div class="criteria-strip">
        <div class="crit">
            <span class="crit-label">Period:</span>
            <span class="crit-value">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</span>
        </div>
        <div class="crit">
            <span class="crit-label">Status Filter:</span>
            <span class="crit-value">{{ $statusFilter ? ucfirst($statusFilter) : 'All statuses' }}</span>
        </div>
        @if($activityFilter)
        <div class="crit">
            <span class="crit-label">Activity:</span>
            <span class="crit-value">{{ $activityFilter }}</span>
        </div>
        @endif
        <div class="crit">
            <span class="crit-label">Total Records:</span>
            <span class="crit-value">{{ $total }}</span>
        </div>
    </div>

    {{-- ── KPI Cards ───────────────────────────────────────────────── --}}
    <div class="kpi-row">
        <div class="kpi-card kpi-total">
            <div class="kpi-number">{{ $total }}</div>
            <div class="kpi-label">Total Events</div>
        </div>
        <div class="kpi-card kpi-done">
            <div class="kpi-number">{{ $doneCount }}</div>
            <div class="kpi-label">Completed</div>
        </div>
        <div class="kpi-card kpi-pending">
            <div class="kpi-number">{{ $pendingCount }}</div>
            <div class="kpi-label">Pending</div>
        </div>
        <div class="kpi-card kpi-pct">
            <div class="kpi-number">{{ $completionPct }}%</div>
            <div class="kpi-label">Completion Rate</div>
        </div>
    </div>

    {{-- ── Completion Progress Bar ─────────────────────────────────── --}}
    <div class="progress-wrap">
        <div class="progress-label">
            <span>Overall completion rate</span>
            <span>{{ $doneCount }} done / {{ $pendingCount }} pending</span>
        </div>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width: {{ $completionPct }}%"></div>
        </div>
    </div>

    {{-- ── Data Tables by Date ─────────────────────────────────────── --}}
    @php $rowNum = 0; @endphp

    @forelse($byDate as $date => $dayLogs)
    @php
        $dayDone    = $dayLogs->where('status','done')->count();
        $dayPending = $dayLogs->where('status','pending')->count();
        $dayTotal   = $dayLogs->count();
    @endphp
    <div class="date-section">
        <div class="date-header">
            <span>{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</span>
            <span class="date-count">{{ $dayTotal }} event(s)</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="col-no">#</th>
                    <th style="width:22%">Activity</th>
                    <th style="width:10%">Category</th>
                    <th style="width:8%">Recurrence</th>
                    <th style="width:7%">Status</th>
                    <th style="width:14%">Updated By</th>
                    <th style="width:7%">Role</th>
                    <th style="width:8%">Time</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dayLogs->sortBy('created_at') as $log)
                @php $rowNum++; @endphp
                <tr>
                    <td class="col-no">{{ $rowNum }}</td>
                    <td style="font-weight:600; color:#111827">{{ $log->activity?->title ?? '—' }}</td>
                    <td>{{ $log->activity?->category ?? '—' }}</td>
                    <td style="text-transform:capitalize">{{ $log->activity?->recurrence ?? '—' }}</td>
                    <td>
                        @if($log->status === 'done')
                            <span class="status-done">✓ Done</span>
                        @else
                            <span class="status-pending">⏳ Pending</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600">{{ $log->actor_name }}</div>
                        @if($log->actor_designation)
                        <div style="font-size:7pt; color:#9ca3af">{{ $log->actor_designation }}</div>
                        @endif
                    </td>
                    <td style="text-transform:capitalize; font-size:7.5pt">{{ $log->actor_role ?? '—' }}</td>
                    <td style="font-size:8pt; font-family: monospace">{{ $log->created_at->format('H:i') }}</td>
                    <td>
                        @if($log->remark)
                            <span class="remark-cell">"{{ $log->remark }}"</span>
                        @else
                            <span class="no-remark">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="section-footer">
                    <td colspan="4" style="text-align:right; padding-right:12px">Day total:</td>
                    <td colspan="5">
                        <span style="color:#1B6B3A">✓ {{ $dayDone }} done</span>
                        &nbsp;&nbsp;
                        <span style="color:#d97706">⏳ {{ $dayPending }} pending</span>
                        &nbsp;&nbsp;
                        <span style="color:#6b7280">{{ $dayTotal > 0 ? round($dayDone / $dayTotal * 100) : 0 }}% complete</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @empty
    <div style="text-align: center; padding: 40px; color: #6b7280; font-size: 11pt;">
        No activity records found for the selected criteria.
    </div>
    @endforelse

    {{-- ── Grand Total Row ─────────────────────────────────────────── --}}
    @if($total > 0)
    <table style="margin-top: 4px;">
        <tbody>
            <tr class="grand-total">
                <td colspan="4" style="text-align: right; padding-right: 12px;">GRAND TOTAL</td>
                <td colspan="5">
                    {{ $total }} records &nbsp;|&nbsp;
                    ✓ {{ $doneCount }} done &nbsp;|&nbsp;
                    ⏳ {{ $pendingCount }} pending &nbsp;|&nbsp;
                    {{ $completionPct }}% completion rate
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- ── Footer ──────────────────────────────────────────────────── --}}
    <div class="report-footer">
        <span>© {{ date('Y') }} Npontu Technologies — Support Activity Tracker</span>
        <span>
            <strong>Period:</strong>
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
            &nbsp;|&nbsp;
            <strong>Prepared by:</strong> {{ $generatedBy }}
            &nbsp;|&nbsp;
            <strong>Generated:</strong> {{ $generatedAt }}
        </span>
    </div>

</div>

{{-- Auto-trigger print dialog when loaded --}}
<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            window.print();
        }, 600);
    });
</script>
</body>
</html>
