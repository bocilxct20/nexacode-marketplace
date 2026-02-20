<?php

namespace App\Livewire\Admin;

use App\Models\PaymentMethod;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Flux;

class PaymentMethodManager extends Component
{
    use WithFileUploads;

    public $paymentMethods;
    public $editingId = null;
    
    public $showModal = false;

    // Form fields
    public $type = 'bank_transfer';
    public $name = '';
    public $account_number = '';
    public $account_name = '';
    public $qris_static = '';
    public $logo;

    public $instructions = [];
    public $is_active = true;
    public $sort_order = 0;

    protected function rules()
    {
        return [
            'type' => 'required|in:bank_transfer,qris,ewallet',
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'qris_static' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'instructions' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }

    public function mount()
    {
        $this->loadPaymentMethods();
    }

    public function loadPaymentMethods()
    {
        $this->paymentMethods = PaymentMethod::orderBy('sort_order')->get();
    }

    public function create()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $method = PaymentMethod::findOrFail($id);
        
        $this->editingId = $id;
        $this->type = $method->type;
        $this->name = $method->name;
        $this->account_number = $method->account_number ?? '';
        $this->account_name = $method->account_name ?? '';
        $this->qris_static = $method->qris_static ?? '';
        $this->instructions = $method->instructions ?? [];
        $this->is_active = $method->is_active;
        $this->sort_order = $method->sort_order;
        
        $this->showModal = true;
    }

    public function save()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->validate();

        $data = [
            'type' => $this->type,
            'name' => $this->name,
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'qris_static' => $this->qris_static,
            'instructions' => array_filter($this->instructions),
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        // Handle logo upload
        if ($this->logo) {
            if ($this->editingId) {
                $method = PaymentMethod::find($this->editingId);
                if ($method && $method->logo) {
                    Storage::disk('public')->delete($method->logo);
                }
            }
            $data['logo'] = $this->logo->store('payment-methods', 'public');
        }

        if ($this->editingId) {
            PaymentMethod::find($this->editingId)->update($data);
            $message = 'Payment method updated successfully';
        } else {
            PaymentMethod::create($data);
            $message = 'Payment method created successfully';
        }

        $this->loadPaymentMethods();
 
        Flux::toast(variant: 'success', heading: 'Berhasil', text: $message);
        $this->showModal = false;
        $this->dispatch('payment-method-saved');
    }

    public function delete($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $method = PaymentMethod::findOrFail($id);
        
        if ($method->logo) {
            Storage::disk('public')->delete($method->logo);
        }
        
        $method->delete();
        
        $this->loadPaymentMethods();
        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Payment method deleted successfully');
        $this->dispatch('payment-method-deleted');
    }

    public function toggleStatus($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $method = PaymentMethod::findOrFail($id);
        $method->update(['is_active' => !$method->is_active]);
        
        $this->loadPaymentMethods();
        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Payment method status updated');
        $this->dispatch('payment-method-toggled');
    }

    public function addInstruction()
    {
        $this->instructions[] = '';
    }

    public function removeInstruction($index)
    {
        unset($this->instructions[$index]);
        $this->instructions = array_values($this->instructions);
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    private function resetForm()
    {
        $this->type = 'bank_transfer';
        $this->name = '';
        $this->account_number = '';
        $this->account_name = '';
        $this->qris_static = '';
        $this->logo = null;
        $this->instructions = [];
        $this->is_active = true;
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function deleteLogo($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $method = PaymentMethod::findOrFail($id);
        if ($method->logo) {
            Storage::disk('public')->delete($method->logo);
            $method->update(['logo' => null]);
        }
        $this->loadPaymentMethods();
        $this->dispatch('payment-method-saved');
    }

    public function render()
    {
        return view('livewire.admin.payment-method-manager');
    }
}
