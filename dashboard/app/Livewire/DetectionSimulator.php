<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class DetectionSimulator extends Component
{
    // Form Inputs
    public string $protocol_type = 'tcp';
    public string $service = 'http';
    public string $flag = 'SF';
    public int $src_bytes = 250;
    public int $hot = 0;
    public int $su_attempted = 0;
    public float $serror_rate = 0.0;
    public float $same_srv_rate = 1.0;
    public float $diff_srv_rate = 0.0;
    public float $dst_host_diff_srv_rate = 0.0;

    // Output Result
    public ?array $result = null;

    public function mount(): void
    {
        $this->analyzePacket();
    }

    public function analyzePacket(): void
    {
        // Simple heuristic rule simulation simulating our CNN-LSTM model predictions
        $prediction = 'Normal';
        $confidence = 0.95 + (rand(0, 49) / 1000); // 95% - 99.9%
        $reasons = [];

        if ($this->serror_rate > 0.7 && $this->same_srv_rate < 0.3) {
            $prediction = 'DoS';
            $reasons[] = 'High SYN error rate (serror_rate > 0.70) combined with low same-service rates indicating resource exhaustion attempts.';
            if ($this->flag !== 'SF') {
                $reasons[] = "Connection state flag ({$this->flag}) supports anomalous status.";
            }
        } elseif ($this->protocol_type === 'icmp' && $this->service === 'private') {
            $prediction = 'Probe';
            $reasons[] = 'ICMP traffic directed to private port ranges, indicating standard mapping or reconnaissance scanning.';
        } elseif ($this->dst_host_diff_srv_rate > 0.6 && $this->diff_srv_rate > 0.5) {
            $prediction = 'Probe';
            $reasons[] = 'Significant rate of connections directed to different services on destination host, resembling active port scanning.';
        } elseif ($this->su_attempted > 0 || $this->hot > 2) {
            $prediction = 'U2R';
            $reasons[] = 'Elevated "hot" indicators (active shell commands or directory modifications) and superuser attempts detected.';
        } elseif ($this->src_bytes > 50000 && $this->hot > 0) {
            $prediction = 'R2L';
            $reasons[] = 'Large packet payload (src_bytes) matched with hot indicator activations; typical of unauthorized remote file insertions.';
        } else {
            $reasons[] = 'Feature values fit within benign traffic thresholds (Normal). Connection flag is normal (SF) with active same-service distribution.';
        }

        $this->result = [
            'prediction' => $prediction,
            'confidence' => round($confidence * 100, 2),
            'reasons' => $reasons,
            'timestamp' => now()->format('Y-m-d H:i:s.u')
        ];
    }

    public function loadScenario(string $scenario): void
    {
        switch ($scenario) {
            case 'benign':
                $this->protocol_type = 'tcp';
                $this->service = 'http';
                $this->flag = 'SF';
                $this->src_bytes = 320;
                $this->hot = 0;
                $this->su_attempted = 0;
                $this->serror_rate = 0.0;
                $this->same_srv_rate = 1.0;
                $this->diff_srv_rate = 0.0;
                $this->dst_host_diff_srv_rate = 0.0;
                break;
            case 'syn_flood':
                $this->protocol_type = 'tcp';
                $this->service = 'private';
                $this->flag = 'S0';
                $this->src_bytes = 0;
                $this->hot = 0;
                $this->su_attempted = 0;
                $this->serror_rate = 1.0;
                $this->same_srv_rate = 0.05;
                $this->diff_srv_rate = 0.95;
                $this->dst_host_diff_srv_rate = 0.85;
                break;
            case 'port_scan':
                $this->protocol_type = 'icmp';
                $this->service = 'private';
                $this->flag = 'SF';
                $this->src_bytes = 8;
                $this->hot = 0;
                $this->su_attempted = 0;
                $this->serror_rate = 0.0;
                $this->same_srv_rate = 0.1;
                $this->diff_srv_rate = 0.9;
                $this->dst_host_diff_srv_rate = 0.95;
                break;
            case 'privilege_escalation':
                $this->protocol_type = 'tcp';
                $this->service = 'telnet';
                $this->flag = 'SF';
                $this->src_bytes = 1420;
                $this->hot = 3;
                $this->su_attempted = 1;
                $this->serror_rate = 0.0;
                $this->same_srv_rate = 1.0;
                $this->diff_srv_rate = 0.0;
                $this->dst_host_diff_srv_rate = 0.0;
                break;
        }

        $this->analyzePacket();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.detection-simulator')->layout('layouts.app');
    }
}
