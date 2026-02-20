<?php

namespace App\Livewire\Admin;

use App\Models\PlatformSetting;
use Livewire\Component;
use Flux;

class SettingsManager extends Component
{
    use \Livewire\WithFileUploads;

    public $settings = [];
    public $newKey = '';
    public $newValue = '';
    public $tab = 'general';

    protected $queryString = ['tab'];

    // Categorized settings properties
    public $site_name = '';
    public $support_email = '';
    public $site_address = '';
    
    // Branding
    public $site_logo;
    public $site_favicon;
    public $existing_logo = '';
    public $existing_favicon = '';

    // Marketplace
    public $commission_rate = 0;
    public $min_payout = 0;
    public $currency_code = 'IDR';
    public $currency_symbol = 'Rp';
    public $auto_approve_authors = false;
    public $auto_approve_products = false;
    public $maintenance_mode = false;

    // SEO
    public $meta_title = '';
    public $meta_description = '';
    
    // Social Media
    public $social_twitter = '';
    public $social_github = '';
    public $social_facebook = '';
    public $social_instagram = '';

    public function mount()
    {
        $this->loadSettings();
        $this->initializeCategorizedSettings();
    }

    public function loadSettings()
    {
        $this->settings = PlatformSetting::all()->toArray();
    }

    public function initializeCategorizedSettings()
    {
        if (!auth()->user()?->isAdmin()) return;

        $this->site_name = $this->getSetting('site_name', 'NexaCode Marketplace');
        $this->support_email = $this->getSetting('support_email', 'support@nexacode.id');
        $this->site_address = $this->getSetting('site_address', '');
        
        $this->existing_logo = $this->getSetting('site_logo', '');
        $this->existing_favicon = $this->getSetting('site_favicon', '');

        $this->commission_rate = (float) $this->getSetting('commission_rate', 10);
        $this->min_payout = (float) $this->getSetting('min_payout', 50000);
        $this->currency_code = $this->getSetting('currency_code', 'IDR');
        $this->currency_symbol = $this->getSetting('currency_symbol', 'Rp');
        $this->auto_approve_authors = (bool) $this->getSetting('auto_approve_authors', false);
        $this->auto_approve_products = (bool) $this->getSetting('auto_approve_products', false);
        $this->maintenance_mode = (bool) $this->getSetting('maintenance_mode', false);

        $this->meta_title = $this->getSetting('meta_title', 'NexaCode - Digital Marketplace');
        $this->meta_description = $this->getSetting('meta_description', 'Premium source code and digital products.');

        $this->social_twitter = $this->getSetting('social_twitter', '');
        $this->social_github = $this->getSetting('social_github', '');
        $this->social_facebook = $this->getSetting('social_facebook', '');
        $this->social_instagram = $this->getSetting('social_instagram', '');
    }

    private function getSetting($key, $default = '')
    {
        $setting = PlatformSetting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function saveCategorizedSettings()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $data = [
            'site_name' => $this->site_name,
            'support_email' => $this->support_email,
            'site_address' => $this->site_address,
            'commission_rate' => $this->commission_rate,
            'min_payout' => $this->min_payout,
            'currency_code' => $this->currency_code,
            'currency_symbol' => $this->currency_symbol,
            'auto_approve_authors' => $this->auto_approve_authors,
            'auto_approve_products' => $this->auto_approve_products,
            'maintenance_mode' => $this->maintenance_mode,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'social_twitter' => $this->social_twitter,
            'social_github' => $this->social_github,
            'social_facebook' => $this->social_facebook,
            'social_instagram' => $this->social_instagram,
        ];

        // Handle Logo Upload
        if ($this->site_logo) {
            $path = $this->site_logo->store('platform', 'public');
            $data['site_logo'] = $path;
            $this->existing_logo = $path;
        }

        // Handle Favicon Upload
        if ($this->site_favicon) {
            $path = $this->site_favicon->store('platform', 'public');
            $data['site_favicon'] = $path;
            $this->existing_favicon = $path;
        }

        foreach ($data as $key => $value) {
            PlatformSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->loadSettings();
        $this->dispatch('setting-updated');
        
        Flux::toast(variant: 'success', heading: 'Success', text: 'Platform settings updated successfully.');
    }

    public function updateSetting($id, $value)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $setting = PlatformSetting::findOrFail($id);
        $setting->update(['value' => $value]);

        $this->initializeCategorizedSettings(); // Sync back
        Flux::toast(variant: 'success', heading: 'Success', text: 'Setting updated successfully.');
        $this->dispatch('setting-updated');
    }

    public function deleteSetting($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $setting = PlatformSetting::findOrFail($id);
        $setting->delete();

        $this->loadSettings();
        $this->initializeCategorizedSettings();
        Flux::toast(variant: 'success', heading: 'Success', text: 'Setting deleted.');
        $this->dispatch('setting-updated');
    }

    public function addSetting()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->validate([
            'newKey' => 'required|unique:platform_settings,key',
            'newValue' => 'required',
        ]);

        PlatformSetting::create([
            'key' => $this->newKey,
            'value' => $this->newValue,
        ]);

        $this->reset(['newKey', 'newValue']);
        $this->loadSettings();
        $this->initializeCategorizedSettings();
        
        Flux::toast(variant: 'success', heading: 'Success', text: 'New setting added.');
        $this->dispatch('setting-updated');
    }

    public function render()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        return view('livewire.admin.settings-manager');
    }
}
