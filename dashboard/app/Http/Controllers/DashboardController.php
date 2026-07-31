<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $metrics = [
            'models' => [
                [
                    'name' => 'CNN-LSTM Baseline (Keras)',
                    'dataset' => 'NSL-KDD',
                    'features' => 41,
                    'accuracy' => 77.70,
                    'f1' => 0.7571,
                    'latency' => 157.66,
                    'size' => 1.86,
                    'status' => 'Confirmed'
                ],
                [
                    'name' => 'CNN-LSTM + BWOA v3 (Keras)',
                    'dataset' => 'NSL-KDD',
                    'features' => 10,
                    'accuracy' => 70.56,
                    'f1' => 0.7127,
                    'latency' => 82.32,
                    'size' => 4.88,
                    'status' => 'Confirmed'
                ],
                [
                    'name' => 'CNN-LSTM + BWOA Quantized (TFLite Float16)',
                    'dataset' => 'NSL-KDD',
                    'features' => 10,
                    'accuracy' => 70.56,
                    'f1' => 0.7127,
                    'latency' => 0.76,
                    'size' => 0.82,
                    'status' => 'PASS'
                ],
                [
                    'name' => 'Transfer Learning Model',
                    'dataset' => 'SWaT',
                    'features' => 51,
                    'accuracy' => 59.95,
                    'f1' => 0.5966,
                    'latency' => 0.12,
                    'size' => 1.76,
                    'status' => 'PASS'
                ]
            ],
            'perClass' => [
                ['name' => 'Normal', 'precision' => 0.9691, 'recall' => 0.6906, 'f1' => 0.8065, 'desc' => 'Benign network traffic; high precision indicating low false alarms.'],
                ['name' => 'DoS', 'precision' => 0.4326, 'recall' => 0.2325, 'f1' => 0.3025, 'desc' => 'Denial of Service; lower recall due to pattern overlap with R2L.'],
                ['name' => 'Probe', 'precision' => 0.6142, 'recall' => 0.7129, 'f1' => 0.6599, 'desc' => 'Reconnaissance and port scanning; best balanced attack detection.'],
                ['name' => 'R2L', 'precision' => 0.0798, 'recall' => 0.2128, 'f1' => 0.1160, 'desc' => 'Remote to Local unauthorized access; highly imbalanced class.'],
                ['name' => 'U2R', 'precision' => 0.0153, 'recall' => 0.3433, 'f1' => 0.0293, 'desc' => 'User to Root privilege escalation; extremely rare (52 train samples).']
            ],
            'features' => [
                'protocol_type' => 'Connection protocol (TCP, UDP, ICMP)',
                'service' => 'Network service destination (HTTP, FTP, SMTP, etc.)',
                'flag' => 'Normal or error status of the connection',
                'src_bytes' => 'Bytes sent from source to destination',
                'hot' => 'Number of hot indicators (e.g., system commands)',
                'su_attempted' => 'Whether superuser (root) was attempted',
                'serror_rate' => 'Percentage of connections with SYN errors',
                'same_srv_rate' => 'Percentage of connections to the same service',
                'diff_srv_rate' => 'Percentage of connections to different services',
                'dst_host_diff_srv_rate' => 'Percentage of connections to different services on destination host'
            ]
        ];

        return view('dashboard.index', compact('metrics'));
    }
}
