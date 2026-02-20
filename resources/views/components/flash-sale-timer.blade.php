<div x-data="{
    endsAt: @js($endsAt->timestamp * 1000),
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
    isExpired: false,
    update() {
        const now = new Date().getTime();
        const distance = this.endsAt - now;

        if (distance < 0) {
            this.isExpired = true;
            return;
        }

        this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
        this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
    },
    init() {
        this.update();
        setInterval(() => this.update(), 1000);
    }
}" x-show="!isExpired" class="p-6 bg-zinc-900 rounded-3xl border border-cyan-500/30 shadow-2xl relative overflow-hidden group">
    {{-- Background Glow --}}
    <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity">
        <div class="absolute -top-1/2 -left-1/2 w-full h-full bg-cyan-500 blur-[80px] rounded-full"></div>
    </div>

    <div class="relative flex flex-col items-center">
        <div class="flex items-center gap-2 mb-4">
            <flux:icon name="bolt" variant="solid" class="size-4 text-cyan-400 animate-pulse" />
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-cyan-400">Offer Ends In</span>
        </div>

        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="text-3xl font-black text-white tabular-nums" x-text="String(days).padStart(2, '0')">00</div>
                <div class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest mt-1">Days</div>
            </div>
            <div class="text-2xl font-black text-zinc-700 pt-1">:</div>
            <div class="flex flex-col items-center">
                <div class="text-3xl font-black text-white tabular-nums" x-text="String(hours).padStart(2, '0')">00</div>
                <div class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest mt-1">Hours</div>
            </div>
            <div class="text-2xl font-black text-zinc-700 pt-1">:</div>
            <div class="flex flex-col items-center">
                <div class="text-3xl font-black text-white tabular-nums" x-text="String(minutes).padStart(2, '0')">00</div>
                <div class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest mt-1">Mins</div>
            </div>
            <div class="text-2xl font-black text-zinc-700 pt-1">:</div>
            <div class="flex flex-col items-center">
                <div class="text-3xl font-black text-white tabular-nums x-text='String(seconds).padStart(2, '0')">00</div>
                <div class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest mt-1 text-cyan-500">Secs</div>
            </div>
        </div>
    </div>
</div>
