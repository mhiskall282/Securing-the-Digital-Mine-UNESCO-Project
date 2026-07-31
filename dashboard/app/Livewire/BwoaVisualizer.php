<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class BwoaVisualizer extends Component
{
    public int $iteration = 0;
    public float $fitness = 0.0;
    public array $selectedFeatures = [];
    public bool $isConverged = false;
    public string $currentPhase = 'Initialization';

    // Total 41 features from NSL-KDD
    public array $allFeatures = [
        'duration', 'protocol_type', 'service', 'flag', 'src_bytes', 'dst_bytes', 'land',
        'wrong_fragment', 'urgent', 'hot', 'num_failed_logins', 'logged_in', 'num_compromised',
        'root_shell', 'su_attempted', 'num_root', 'num_file_creations', 'num_shells',
        'num_access_files', 'num_outbound_cmds', 'is_host_login', 'is_guest_login', 'count',
        'srv_count', 'serror_rate', 'srv_serror_rate', 'rerror_rate', 'srv_rerror_rate',
        'same_srv_rate', 'diff_srv_rate', 'srv_diff_host_rate', 'dst_host_count', 'dst_host_srv_count',
        'dst_host_same_srv_rate', 'dst_host_diff_srv_rate', 'dst_host_same_src_port_rate',
        'dst_host_srv_diff_host_rate', 'dst_host_serror_rate', 'dst_host_srv_serror_rate',
        'dst_host_rerror_rate', 'dst_host_srv_rerror_rate'
    ];

    // The final 10 selected features
    public array $finalFeatures = [
        'protocol_type', 'service', 'flag', 'src_bytes', 'hot', 'su_attempted', 
        'serror_rate', 'same_srv_rate', 'diff_srv_rate', 'dst_host_diff_srv_rate'
    ];

    public function mount(): void
    {
        $this->resetSimulation();
    }

    public function resetSimulation(): void
    {
        $this->iteration = 0;
        $this->fitness = 0.4285;
        $this->isConverged = false;
        $this->currentPhase = 'Initialization';
        // Randomly select half of all features initially
        $this->selectedFeatures = array_intersect(
            $this->allFeatures,
            array_slice($this->allFeatures, 0, 20)
        );
    }

    public function nextStep(): void
    {
        if ($this->isConverged) {
            return;
        }

        $this->iteration++;

        if ($this->iteration >= 23) {
            $this->iteration = 23;
            $this->selectedFeatures = $this->finalFeatures;
            $this->fitness = 0.8471;
            $this->isConverged = true;
            $this->currentPhase = 'Converged (Optimal Subset Found)';
            return;
        }

        // Simulate BWOA phases
        if ($this->iteration < 8) {
            $this->currentPhase = 'Exploration Phase (Random Whale Search)';
            // Slowly reduce features and improve fitness
            $this->fitness = round(0.4285 + ($this->iteration * 0.015), 4);
            $shuffle = $this->allFeatures;
            shuffle($shuffle);
            $this->selectedFeatures = array_unique(array_merge(
                array_slice($shuffle, 0, 22 - $this->iteration),
                array_slice($this->finalFeatures, 0, min($this->iteration, 6))
            ));
        } elseif ($this->iteration < 18) {
            $this->currentPhase = 'Bubble-net Attacking Phase (Spiral Mutation)';
            $this->fitness = round(0.55 + (($this->iteration - 8) * 0.02), 4);
            $shuffle = array_diff($this->allFeatures, $this->finalFeatures);
            shuffle($shuffle);
            $this->selectedFeatures = array_unique(array_merge(
                array_slice($shuffle, 0, max(1, 14 - $this->iteration)),
                array_slice($this->finalFeatures, 0, min($this->iteration, 9))
            ));
        } else {
            $this->currentPhase = 'Shrinking Encircling Mechanism';
            $this->fitness = round(0.75 + (($this->iteration - 18) * 0.018), 4);
            $shuffle = array_diff($this->allFeatures, $this->finalFeatures);
            shuffle($shuffle);
            $this->selectedFeatures = array_unique(array_merge(
                array_slice($shuffle, 0, 2),
                $this->finalFeatures
            ));
        }
    }

    public function fastForward(): void
    {
        while (!$this->isConverged) {
            $this->nextStep();
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.bwoa-visualizer')->layout('layouts.app');
    }
}
