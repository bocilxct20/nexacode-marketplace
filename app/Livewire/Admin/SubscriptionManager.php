<?php

namespace App\Livewire\Admin;

use App\Models\SubscriptionPlan;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Flux;

class SubscriptionManager extends Component
{
    public $editingPlan = null;
    public $planForm = [
        'name' => '',
        'price' => 0,
        'commission_rate' => 0,
        'features' => [],
        'is_active' => true,
        'is_default' => false,
        'allow_trial' => false,
        'is_elite' => false,
    ];

    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $newFeature = '';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function edit(SubscriptionPlan $plan)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->editingPlan = $plan;
        $this->planForm = [
            'name' => $plan->name,
            'price' => $plan->price,
            'commission_rate' => $plan->commission_rate,
            'features' => $plan->features ?? [],
            'is_active' => $plan->is_active,
            'is_default' => $plan->is_default,
            'allow_trial' => $plan->allow_trial,
            'is_elite' => $plan->is_elite,
        ];

        Flux::modal('edit-plan-modal')->show();
    }

    public function addFeature()
    {
        if (trim($this->newFeature)) {
            $this->planForm['features'][] = trim($this->newFeature);
            $this->newFeature = '';
        }
    }

    public function removeFeature($index)
    {
        unset($this->planForm['features'][$index]);
        $this->planForm['features'] = array_values($this->planForm['features']);
    }

    public function save()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->validate([
            'planForm.name' => 'required|string|max:255',
            'planForm.price' => 'required|numeric|min:0',
            'planForm.commission_rate' => 'required|numeric|min:0|max:100',
            'planForm.features' => 'array',
        ]);

        $this->editingPlan->update($this->planForm);

        Flux::modal('edit-plan-modal')->close();

        $this->dispatch('toast', 
            variant: 'success',
            heading: 'Plan Updated',
            text: "{$this->editingPlan->name} plan has been updated successfully."
        );

        $this->editingPlan = null;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $plans = SubscriptionPlan::orderBy($this->sortBy, $this->sortDirection)->get();

        return view('livewire.admin.subscription-manager', [
            'plans' => $plans,
        ]);
    }
}
