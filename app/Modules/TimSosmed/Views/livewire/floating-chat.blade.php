<div wire:poll.3s x-data="{ isOpen: @entangle('isOpen'), isLarge: @entangle('isLarge') }" style="position: fixed; bottom: 25px; right: 25px; z-index: 999999;">

    @if ($isOpen)
        <div id="chat-window"
            style="display: flex; flex-direction: column; background: white; border-radius: 20px; margin-bottom: 15px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e5e7eb; overflow: hidden; resize: both; min-width: 350px; min-height: 450px; width: {{ $isLarge ? '550px' : '380px' }}; height: {{ $isLarge ? '650px' : '520px' }};">

            <div
                style="flex-shrink: 0; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: white;">
                <b style="font-size: 14px;">Diskusi Tim</b>
                <div style="display: flex; gap: 10px;">
                    <button wire:click="toggleSize" type="button"
                        style="background:none; border:none; color:white; cursor:pointer;">⤢</button>
                    <button wire:click="toggleOpen" type="button"
                        style="background:none; border:none; color:white; cursor:pointer; font-size: 20px;">×</button>
                </div>
            </div>

            <div id="chat-body-container" wire:poll.visible.3s
                style="flex: 1; overflow-y: auto; padding: 20px; background-color: #f9fafb; display: flex; flex-direction: column; gap: 15px;">
                @forelse($messages as $msg)
                    <div wire:key="msg-{{ $msg->id }}"
                        style="display: flex; flex-direction: column; align-items: {{ $msg->user_id === auth()->id() ? 'flex-end' : 'flex-start' }};">

                        @if ($msg->created_at > $lastSeenAt && $msg->user_id !== auth()->id())
                            <span
                                style="background: #ef4444; color: white; font-size: 8px; font-weight: 900; padding: 2px 8px; border-radius: 10px; margin-bottom: 4px; border: 1px solid white;">NEW</span>
                        @endif

                        <div
                            style="position: relative; max-width: 85%; padding: 10px 14px; border-radius: 15px; font-size: 13px;
                            {{ $msg->user_id === auth()->id() ? 'background: #4f46e5; color: white; border-bottom-right-radius: 2px;' : 'background: white; color: #1f2937; border: 1px solid #e5e7eb; border-bottom-left-radius: 2px;' }}">
                            <div class="prose prose-sm {{ $msg->user_id === auth()->id() ? 'prose-invert' : '' }}"
                                style="font-size: 12.5px;">
                                {!! $msg->body !!}
                            </div>
                        </div>
                        <small
                            style="font-size: 9px; color: #94a3b8; margin-top: 4px;">{{ strtoupper($msg->user->name) }}
                            • {{ $msg->created_at->format('H:i') }}</small>
                    </div>
                @empty
                    <div style="margin: auto; color: #cbd5e1; font-size: 12px; font-style: italic;">Mulai diskusi...
                    </div>
                @endforelse
            </div>

            <div style="flex-shrink: 0; padding: 15px; background: white; border-top: 1px solid #f3f4f6;">
                <div onkeydown="if(event.key==='Enter' && !event.shiftKey){ event.preventDefault(); @this.send(); }"
                    style="background: #f9fafb; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 4px; margin-bottom: 10px; overflow: hidden;">
                    {{ $this->form }}
                </div>
                <button wire:click="send" type="button"
                    style="width: 100%; background: #4f46e5; color: white; height: 40px; border: none; border-radius: 10px; cursor: pointer; font-weight: 700;">KIRIM
                    PESAN</button>
            </div>
        </div>
    @endif

    <div style="display: flex; justify-content: flex-end;">
        <button wire:click="toggleOpen" type="button"
            style="width: 65px; height: 65px; background: #4f46e5; color: white; border-radius: 50%; border: none; cursor: pointer; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4); display: flex; align-items: center; justify-content: center; position: relative; outline: none;">
            @if ($isOpen)
                ▼
            @else
                <svg style="width: 30px; height: 30px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                @if (isset($unreadCount) && $unreadCount > 0)
                    <span
                        style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; min-width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 900; border: 3px solid white;">{{ $unreadCount }}</span>
                @endif
            @endif
        </button>
    </div>

    <script>
        // 1. Fungsi Scroll (Tetap dipertahankan dengan optimasi jeda)
        function doScroll() {
            const container = document.getElementById('chat-body-container');
            if (container) {
                setTimeout(() => {
                    container.scrollTo({
                        top: container.scrollHeight,
                        behavior: 'smooth'
                    });
                }, 200); // Jeda 200ms lebih aman untuk render DOM
            }
        }

        // 2. JURUS PAMUNGKAS: Hentikan error "Uncaught (in promise)" secara global
        // Ini akan menangkap error "status: null" sebelum sempat tampil merah di console
        window.addEventListener('unhandledrejection', function(event) {
            if (event.reason && (
                    event.reason.status === null ||
                    event.reason.status === 0 ||
                    event.reason.body === null
                )) {
                event.preventDefault(); // Mencegah log error ke console
                return;
            }
        });

        // 3. Setup Livewire
        document.addEventListener('livewire:init', () => {
            // Hook Request untuk menangkap kegagalan transmisi
            Livewire.hook('request', ({
                fail
            }) => {
                fail(({
                    status,
                    preventDefault
                }) => {
                    if (status === null || status === 0) {
                        preventDefault();
                    }
                });
            });

            // Dengerin event 'pesan-masuk' dari Livewire 3
            Livewire.on('pesan-masuk', () => doScroll());
        });

        // 4. Trigger tambahan
        window.addEventListener('pesan-masuk', () => doScroll());

        // Auto-scroll saat ada klik tombol (seperti buka chat)
        document.addEventListener('click', function(e) {
            if (e.target.closest('button')) {
                doScroll();
            }
        });
    </script>
</div>
