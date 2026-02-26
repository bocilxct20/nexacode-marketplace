<?php

namespace App\Livewire\Global;

use App\Models\ProductAudit;
use Livewire\Component;

class ProductAuditBadge extends Component
{
    public $productId;
    public $securityAudit;
    public $qualityAudit;

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->loadAudits();
    }

    public function loadAudits()
    {
        $this->securityAudit = ProductAudit::getLatestAudit($this->productId, 'security');
        $this->qualityAudit = ProductAudit::getLatestAudit($this->productId, 'quality');
    }

    public function render()
    {
        return view('livewire.global.product-audit-badge');
    }
}
