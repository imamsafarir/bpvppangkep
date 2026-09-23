<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Modules\TimSosmed\Models\Content;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles {
        hasRole as traitHasRole;
        hasAnyRole as traitHasAnyRole;
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($this->role === 'admin') {
            if ($roles === 'super_admin' || (is_array($roles) && in_array('super_admin', $roles))) {
                return true;
            }
        }

        return $this->traitHasRole($roles, $guard);
    }

    public function hasAnyRole(...$roles): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return $this->traitHasAnyRole(...$roles);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELASI TIM SOSMED ---

    public function instrukturContents()
    {
        return $this->hasMany(Content::class, 'instruktur_id');
    }

    public function plannerContents()
    {
        return $this->hasMany(Content::class, 'planner_id');
    }

    public function editorContents()
    {
        return $this->hasMany(Content::class, 'editor_id');
    }

    public function adminContents()
    {
        return $this->hasMany(Content::class, 'admin_id');
    }

    public function activeTasks()
    {
        return Content::where(function ($query) {
            $query->where('instruktur_id', $this->id)
                ->orWhere('planner_id', $this->id)
                ->orWhere('editor_id', $this->id)
                ->orWhere('admin_id', $this->id);
        })->where('status', '!=', 'selesai');
    }
}
