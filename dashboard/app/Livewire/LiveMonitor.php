<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class LiveMonitor extends Component
{
    public bool $isAutoSniffing = true;

    public function toggleStreaming(): void
    {
        $this->isAutoSniffing = !$this->isAutoSniffing;
    }

    public function triggerIntermittentSniffer(): void
    {
        $this->generateSniffedFlows(rand(4, 7));
    }

    public function autoSniffTick(): void
    {
        if ($this->isAutoSniffing) {
            $this->generateSniffedFlows(rand(1, 2));
        }
    }

    private function generateSniffedFlows(int $count): void
    {
        $orgId = auth()->user() ? auth()->user()->organization_id : 1;
        
        for ($i = 0; $i < $count; $i++) {
            $isAnomaly = rand(0, 100) < 22;
            $predictions = ['DoS', 'Probe', 'U2R', 'R2L'];
            $prediction = $isAnomaly ? $predictions[array_rand($predictions)] : 'Normal';
            $confidence = round(92.0 + (rand(0, 750) / 100), 2);
            $latency = round(0.15 + (rand(0, 80) / 100), 2);

            try {
                DB::table('live_network_flows')->insert([
                    'organization_id' => $orgId,
                    'device_id' => null,
                    'protocol_type' => ['tcp', 'udp', 'icmp'][array_rand(['tcp', 'udp', 'icmp'])],
                    'service' => ['http', 'domain', 'private', 'telnet'][array_rand(['http', 'domain', 'private', 'telnet'])],
                    'flag' => $prediction === 'Normal' ? 'SF' : 'S0',
                    'src_bytes' => rand(64, 2048),
                    'hot' => $prediction === 'U2R' ? rand(2, 5) : 0,
                    'su_attempted' => 0,
                    'serror_rate' => $prediction === 'DoS' ? 0.95 : 0.0,
                    'same_srv_rate' => 0.85,
                    'diff_srv_rate' => 0.15,
                    'dst_host_diff_srv_rate' => 0.05,
                    'prediction' => $prediction,
                    'confidence' => $confidence,
                    'latency_ms' => $latency,
                ]);
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }

    public function clearLogs(): void
    {
        try {
            $orgId = auth()->user() ? auth()->user()->organization_id : 1;
            DB::table('live_network_flows')
                ->where('organization_id', $orgId)
                ->delete();
        } catch (\Exception $e) {
            // Ignore
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $orgId = auth()->user() ? auth()->user()->organization_id : 1;

        $totalFlows = 0;
        $normalCount = 0;
        $attackCount = 0;
        $classDistribution = [
            'Normal' => 0,
            'DoS' => 0,
            'Probe' => 0,
            'R2L' => 0,
            'U2R' => 0
        ];
        
        $latestFlows = [];

        try {
            $stats = DB::table('live_network_flows')
                ->select('prediction', DB::raw('count(*) as count'))
                ->where('organization_id', $orgId)
                ->groupBy('prediction')
                ->get();

            foreach ($stats as $stat) {
                if (isset($classDistribution[$stat->prediction])) {
                    $classDistribution[$stat->prediction] = (int)$stat->count;
                }
                $totalFlows += (int)$stat->count;
                if ($stat->prediction === 'Normal') {
                    $normalCount += (int)$stat->count;
                } else {
                    $attackCount += (int)$stat->count;
                }
            }

            $latestFlows = DB::table('live_network_flows')
                ->where('organization_id', $orgId)
                ->orderBy('id', 'desc')
                ->limit(15)
                ->get();

        } catch (\Exception $e) {
            // Ignore
        }

        $anomalyRate = $totalFlows > 0 ? round(($attackCount / $totalFlows) * 100, 2) : 0.0;

        return view('livewire.live-monitor', [
            'latestFlows' => $latestFlows,
            'totalFlows' => $totalFlows,
            'normalCount' => $normalCount,
            'attackCount' => $attackCount,
            'anomalyRate' => $anomalyRate,
            'classDistribution' => $classDistribution
        ])->layout('layouts.app');
    }
}
