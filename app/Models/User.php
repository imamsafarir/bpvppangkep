<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Modules\TimSosmed\Models\Content;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles {
        hasRole as traitHasRole;
        hasAnyRole as traitHasAnyRole;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getRolesListAttribute(): array
    {
        $role = $this->role ?? '';
        if (blank($role)) {
            return [];
        }

        if (is_array($role)) {
            return $role;
        }

        if (str_starts_with($role, '[') && str_ends_with($role, ']')) {
            $decoded = json_decode($role, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return array_values(array_filter(array_map('trim', explode(',', $role))));
    }

    public function getAllRoleNames(): array
    {
        $spatieRoles = $this->roles ? $this->roles->pluck('name')->toArray() : [];
        $columnRoles = $this->roles_list;

        return array_values(array_unique(array_filter(array_merge($spatieRoles, $columnRoles))));
    }

    public function hasRoleName(string ...$roles): bool
    {
        $allRoles = $this->getAllRoleNames();
        foreach ($roles as $r) {
            if (in_array($r, $allRoles, true)) {
                return true;
            }
        }

        return false;
    }

    public function getMedsosRoleBadgesAttribute(): array
    {
        $roles = $this->getAllRoleNames();
        $badges = [];

        $roleDefinitions = [
            ['keys' => ['super_admin', 'admin'], 'label' => 'Administrator', 'icon' => '👑', 'color' => 'danger'],
            ['keys' => ['medsos_planner', 'planner'], 'label' => 'Planner', 'icon' => '📋', 'color' => 'warning'],
            ['keys' => ['medsos_editor', 'editor'], 'label' => 'Editor', 'icon' => '🎨', 'color' => 'success'],
            ['keys' => ['medsos_admin_platform', 'admin_platform'], 'label' => 'Admin Platform', 'icon' => '🚀', 'color' => 'info'],
            ['keys' => ['medsos_instruktur', 'instruktur'], 'label' => 'Instruktur', 'icon' => '👨‍🏫', 'color' => 'primary'],
        ];

        foreach ($roleDefinitions as $def) {
            foreach ($def['keys'] as $k) {
                if (in_array($k, $roles, true)) {
                    $badges[$def['label']] = [
                        'label' => $def['label'],
                        'icon' => $def['icon'],
                        'color' => $def['color'],
                    ];
                    break;
                }
            }
        }

        return array_values($badges);
    }

    public function scopeMedsosTeam(Builder $query): Builder
    {
        $medsosRoles = [
            'super_admin',
            'admin',
            'admin_platform',
            'medsos_admin_platform',
            'planner',
            'medsos_planner',
            'editor',
            'medsos_editor',
            'instruktur',
            'medsos_instruktur',
        ];

        return $query->where(function (Builder $q) use ($medsosRoles) {
            $q->whereHas('roles', fn(Builder $sub) => $sub->whereIn('name', $medsosRoles));

            foreach ($medsosRoles as $role) {
                $q->orWhere('role', $role)
                    ->orWhere('role', 'like', "%\"{$role}\"%")
                    ->orWhere('role', 'like', "%,{$role},%")
                    ->orWhere('role', 'like', "{$role},%")
                    ->orWhere('role', 'like', "%,{$role}");
            }
        });
    }

    public function isAdmin(): bool
    {
        return $this->hasRoleName('admin', 'super_admin');
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->hasRoleName('staff', 'pegawai');
    }

    public function isShortlink(): bool
    {
        return $this->isAdmin() || $this->hasRoleName('shortlink');
    }

    public function isWebsite(): bool
    {
        return $this->isAdmin() || $this->hasRoleName('website', 'pengelola_web');
    }

    public function isInstruktur(): bool
    {
        return $this->isAdmin() || $this->hasRoleName('instruktur', 'medsos_instruktur');
    }

    public function isMedsosPlanner(): bool
    {
        return $this->isAdmin() || $this->hasRoleName('planner', 'medsos_planner');
    }

    public function isMedsosEditor(): bool
    {
        return $this->isAdmin() || $this->hasRoleName('editor', 'medsos_editor');
    }

    public function isMedsosAdminPlatform(): bool
    {
        return $this->isAdmin() || $this->hasRoleName('admin_platform', 'medsos_admin_platform');
    }

    public function isMedsosTeam(): bool
    {
        return $this->isAdmin() || in_array(true, [
            $this->isMedsosPlanner(),
            $this->isMedsosEditor(),
            $this->isMedsosAdminPlatform(),
            $this->isInstruktur(),
        ], true);
    }

    public function getDefaultDashboardUrl(): string
    {
        if ($this->isAdmin()) {
            return url('/admin');
        }

        if ($this->isMedsosTeam()) {
            return url('/admin/calendar-page');
        }

        if ($this->isShortlink()) {
            return url('/admin/manage-shortlink');
        }

        if ($this->isWebsite()) {
            return url('/admin/manage-profil');
        }

        return url('/admin');
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $rolesArray = is_array($roles) ? $roles : [$roles];
        foreach ($rolesArray as $role) {
            if ($this->hasRoleName($role)) {
                return true;
            }
        }

        return $this->traitHasRole($roles, $guard);
    }

    public function hasAnyRole(...$roles): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $flattened = collect($roles)->flatten()->toArray();
        foreach ($flattened as $role) {
            if ($this->hasRoleName($role)) {
                return true;
            }
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
