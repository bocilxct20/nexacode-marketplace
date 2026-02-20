<?php

namespace App\Livewire\Admin;

use App\Models\BuyerReport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Flux;

class BuyerReportsManager extends Component
{
    use WithPagination;

    public $filterStatus = 'all'; // all, pending, resolved
    public $filterCategory = 'all';
    public $search = '';

    // Report Detail Modal
    public $selectedReport = null;
    public $showDetailModal = false;
    public $adminNotes = '';

    public function selectReport($reportId)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->selectedReport = BuyerReport::with(['author', 'buyer', 'conversation', 'admin'])->findOrFail($reportId);
        $this->adminNotes = $this->selectedReport->admin_notes ?? '';
        $this->showDetailModal = true;

        // Auto-assign to current admin if pending
        if ($this->selectedReport->status === \App\Enums\BuyerReportStatus::PENDING) {
            $this->selectedReport->assignToAdmin(Auth::id());
            $this->selectedReport->refresh();
        }
    }

    public function markAsResolved()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        if (!$this->selectedReport) return;

        $this->selectedReport->markAsResolved(Auth::id(), $this->adminNotes);
        
        $this->showDetailModal = false;
        $this->selectedReport = null;
        $this->adminNotes = '';
        
        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Laporan telah ditandai sebagai selesai.');
    }

    public function markAsDismissed()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        if (!$this->selectedReport) return;

        $this->selectedReport->markAsDismissed(Auth::id(), $this->adminNotes);
        
        $this->showDetailModal = false;
        $this->selectedReport = null;
        $this->adminNotes = '';
        
        Flux::toast(variant: 'success', heading: 'Berhasil', text: 'Laporan telah ditolak.');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $query = BuyerReport::with(['author', 'buyer', 'conversation'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($this->filterStatus === 'pending') {
            $query->where('status', \App\Enums\BuyerReportStatus::PENDING);
        } elseif ($this->filterStatus === 'resolved') {
            $query->whereIn('status', [\App\Enums\BuyerReportStatus::RESOLVED, \App\Enums\BuyerReportStatus::DISMISSED]);
        }

        // Filter by category
        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        // Search by buyer or author name
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('buyer', function($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('author', function($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        $reports = $query->paginate(20);

        return view('livewire.admin.buyer-reports-manager', [
            'reports' => $reports,
        ]);
    }
}
