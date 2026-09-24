<?php

namespace App\Modules\TimSosmed\Livewire;

use App\Modules\TimSosmed\Models\Comment;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ContentComments extends Component
{
    public ?int $contentId = null;
    public string $newComment = '';

    public function mount($record = null, ?int $contentId = null): void
    {
        if ($record instanceof \Illuminate\Database\Eloquent\Model) {
            $this->contentId = (int) $record->getKey();
        } elseif (is_numeric($record)) {
            $this->contentId = (int) $record;
        } elseif ($contentId) {
            $this->contentId = $contentId;
        }
    }

    public function addComment(): void
    {
        if (! $this->contentId) {
            return;
        }

        $this->newComment = trim($this->newComment);

        $this->validate([
            'newComment' => ['required', 'string', 'min:1', 'max:2000'],
        ], [
            'newComment.required' => 'Komentar tidak boleh kosong.',
            'newComment.max' => 'Komentar maksimal 2000 karakter.',
        ]);

        Comment::create([
            'content_id' => $this->contentId,
            'user_id' => Auth::id(),
            'body' => $this->newComment,
        ]);

        $this->reset('newComment');

        $this->dispatch('comment-added');

        Notification::make()
            ->title('Komentar berhasil dikirim')
            ->success()
            ->send();
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::find($commentId);

        if (! $comment) {
            return;
        }

        $user = Auth::user();

        // Hanya pembuat komentar atau admin yang boleh menghapus
        if ($user && ($user->id === $comment->user_id || $user->isAdmin())) {
            $comment->delete();

            Notification::make()
                ->title('Komentar dihapus')
                ->success()
                ->send();
        }
    }

    public function render()
    {
        $comments = $this->contentId
            ? Comment::where('content_id', $this->contentId)->with('user')->oldest()->get()
            : collect();

        return view('timsosmed::livewire.content-comments', [
            'comments' => $comments,
        ]);
    }
}
