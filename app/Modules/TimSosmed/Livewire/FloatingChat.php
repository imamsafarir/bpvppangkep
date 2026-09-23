<?php

namespace App\Modules\TimSosmed\Livewire;

use App\Modules\TimSosmed\Models\Comment;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class FloatingChat extends Component implements HasForms
{
    use InteractsWithForms;

    public bool $isOpen = false;

    public bool $isLarge = false;

    public ?array $data = [];

    public $lastSeenAt;

    public $lastMessageId = 0; // Tambahkan ini untuk pemicu scroll

    public function mount(): void
    {
        $this->form->fill();
        $this->lastSeenAt = Session::get('chat_last_seen', now()->subMinutes(5)->toDateTimeString());
    }

    public function toggleOpen(): void
    {
        $this->isOpen = ! $this->isOpen;
        if (! $this->isOpen) {
            $this->lastSeenAt = now()->toDateTimeString();
            Session::put('chat_last_seen', $this->lastSeenAt);
        }
    }

    public function toggleSize(): void
    {
        $this->isLarge = ! $this->isLarge;
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                RichEditor::make('body')
                    ->hiddenLabel()
                    ->placeholder('Ketik pesan...')
                    ->toolbarButtons(['bold', 'italic', 'link'])
                    ->required()
                    ->extraAttributes([
                        // Perbaikan: min-height kecil agar rapi, scroll muncul otomatis jika lebih dari 150px
                        'style' => 'min-height: 40px; max-height: 150px; overflow-y: auto;',
                    ]),
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $state = $this->form->getState();
        if (empty(trim(strip_tags($state['body'])))) {
            return;
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'body' => $state['body'],
        ]);

        $this->lastMessageId = $comment->id;
        $this->lastSeenAt = now()->toDateTimeString();
        Session::put('chat_last_seen', $this->lastSeenAt);

        $this->form->fill();
        $this->dispatch('pesan-masuk'); // Pemicu scroll instan
    }

    public function render()
    {
        $messages = Comment::with('user')->latest()->limit(20)->get()->reverse();

        // CEK APAKAH ADA PESAN BARU (LEWAT POLLING)
        $latestId = $messages->last()?->id ?? 0;
        if ($this->isOpen && $latestId > $this->lastMessageId) {
            $this->lastMessageId = $latestId;
            $this->dispatch('pesan-masuk'); // Kirim sinyal scroll ke browser
        }

        $unreadCount = Comment::where('created_at', '>', $this->lastSeenAt)
            ->where('user_id', '!=', Auth::id())
            ->count();

        return view('timsosmed::livewire.floating-chat', compact('messages', 'unreadCount'));
    }
}
