<?php

namespace App\Livewire\Author;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlanSelection extends Component
{
    public $checkoutPlanId = null;
    public $showCheckoutModal = false;

    public function selectPlan($planId)
    {
        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($planId);

        // If it's a paid plan, enter checkout mode
        if ($plan->price > 0) {
            $this->checkoutPlanId = $plan->id;
            $this->showCheckoutModal = true;
            return;
        }

        // Logic for free/default plans remains instant
        $user->update([
            'subscription_plan_id' => $plan->id,
            'trial_ends_at' => null,
        ]);

        \Flux::toast(
            variant: 'success',
            heading: 'Plan Updated',
            text: "You are now on the {$plan->name} tier. Welcome to the elite!",
        );

        return redirect()->route('author.dashboard');
    }

    public function resetCheckout()
    {
        $this->checkoutPlanId = null;
        $this->showCheckoutModal = false;
    }

    public function processPayment()
    {
        if (!$this->checkoutPlanId) return;

        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($this->checkoutPlanId);

        $order = \App\Models\Order::forceCreate([
            'buyer_id' => $user->id,
            'type' => 'subscription',
            'total_amount' => $plan->price,
            'status' => 'pending',
            'payment_method' => 'Waiting Selection',
            'payment_method_id' => null,
            'expires_at' => now()->addHours(24),
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'subscription_plan_id' => $plan->id,
            'price' => $plan->price,
        ]);

        // Load relationships for email
        $order->load(['items.subscriptionPlan', 'buyer']);

        // Send Order Confirmation Email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\OrderConfirmation($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
        }

        return redirect()->route('checkout.payment', $order);
    }

    public function startTrial($planId)
    {
        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($planId);

        if (!$plan->allow_trial) {
            abort(403, 'Trials are only available for permitted plans.');
        }

        if ($user->hasUsedTrial()) {
            $this->dispatch('toast', variant: 'error', heading: 'Trial Unavailable', text: 'You have already used your free trial.');
            return;
        }

        $user->update([
            'subscription_plan_id' => $plan->id,
            'trial_ends_at' => now()->addDays(7),
            'has_used_trial' => true,
        ]);

        // Send Welcome Trial Email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\TrialStarted($user, $plan));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send trial started email: ' . $e->getMessage());
        }

        \Flux::toast(
            variant: 'success',
            heading: 'Trial Activated',
            text: "Your 7-day free trial of {$plan->name} has started. Enjoy the premium perks!",
        );

        return redirect()->route('author.dashboard');
    }

    public function render()
    {
        return view('livewire.author.plan-selection', [
            'plans' => SubscriptionPlan::where('is_active', true)->orderBy('price', 'asc')->get(),
            'user' => Auth::user(),
            'currentPlanId' => Auth::user()->subscription_plan_id,
            'paymentMethods' => \App\Models\PaymentMethod::where('is_active', true)->get(),
            'checkoutPlan' => $this->checkoutPlanId ? SubscriptionPlan::find($this->checkoutPlanId) : null,
            'showCheckoutModal' => $this->showCheckoutModal,
        ])->layout('layouts.author');
    }
}
