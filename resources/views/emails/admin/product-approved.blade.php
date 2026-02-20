@component('emails.layouts.nexacode', [
    'title' => 'Product Approval Notification',
    'actionText' => 'View Product',
    'actionUrl' => route('products.show', $product->slug),
    'actionColor' => 'success'
])

# Your Product is Live!

Your product **{{ $product->name }}** has been approved and is now available for purchase on NexaCode Marketplace.

---

## Product Information
**Name:** {{ $product->name }}  
**Category:** {{ $product->category->name ?? 'Uncategorized' }}  
**Price:** Rp {{ number_format($product->price, 0, ',', '.') }}  
**Status:** Live

---

### Next Steps
Your product can now be discovered by buyers through search and category browsing. Monitor your sales performance through the Author Dashboard.

Make sure to respond to buyer inquiries and update your product regularly to maintain quality.

@endcomponent
