<?php

namespace App\Livewire\Checkout;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Flux;

class PaymentProofUpload extends Component
{
    use WithFileUploads;

    public Order $order;
    public $photos = [];

    protected $rules = [
        'photos.*' => 'required|image|max:10240', // 10MB per photo
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function removePhoto($index)
    {
        array_splice($this->photos, $index, 1);
    }

    public function save()
    {
        $this->validate();

        if (empty($this->photos)) {
            $this->addError('photos', 'Please select at least one file.');
            return;
        }

        // Take the first photo as the primary proof (as the model currently supports one)
        $photo = $this->photos[0];
        
        $path = $photo->store('payment-proofs', 'public');

        $this->order->update([
            'payment_proof' => $path,
            'payment_proof_uploaded_at' => now(),
            'status' => 'pending_verification',
        ]);

        Flux::toast(
            variant: 'success',
            heading: 'Proof Uploaded',
            text: 'Your payment proof has been uploaded successfully. We will verify it shortly.',
        );

        return redirect()->route('payment.show', $this->order);
    }

    public function render()
    {
        return view('livewire.checkout.payment-proof-upload');
    }
}
