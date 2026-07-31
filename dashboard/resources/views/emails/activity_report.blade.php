<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Support Activity Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f5;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #1B6B3A;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #d1fae5;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px;
        }
        .summary {
            margin-bottom: 25px;
            font-size: 15px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        th {
            background-color: #f8fafc;
            text-align: left;
            padding: 10px;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #edf2f7;
            color: #0f172a;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-done {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-pending {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            background-color: #0f1a14;
            color: #a7f3d0;
            padding: 20px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Support Activity Report</h1>
            <p>NPONTU TECHNOLOGIES</p>
        </div>
        <div class="content">
            <div class="summary">
                <p>Hello,</p>
                @if($customMessage)
                    <p style="white-space: pre-wrap; margin-bottom: 20px; font-style: italic; color: #475569;">
                        "{!! nl2br(e($customMessage)) !!}"
                    </p>
                @endif
                <p>Please find the compiled support activity log report for the period <strong>{{ $from }}</strong> to <strong>{{ $to }}</strong>.</p>
                <p>A total of <strong>{{ $logs->count() }}</strong> status change entries were recorded during this period.</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Updated By</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->date->format('d M Y') }}</td>
                        <td>{{ $log->activity?->title ?? '—' }}</td>
                        <td>
                            @if($log->status === 'done')
                                <span class="badge badge-done">Done</span>
                            @else
                                <span class="badge badge-pending">Pending</span>
                            @endif
                        </td>
                        <td>{{ $log->actor_name }}</td>
                        <td>{{ $log->remark ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Npontu Technologies. Internal operational updates.
        </div>
    </div>
</body>
</html>
