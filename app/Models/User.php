<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Supported operational staff roles displayed in user forms.
     *
     * @var list<string>
     */
    public const ROLES = [
        'Store Administrator',
        'Shift Supervisor',
        'Cashier',
        'Inventory Clerk',
        'Staff Member',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'full_name',
        'name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Auto-generate a unique POS username when created via Breeze registration or factories.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (empty($user->username)) {
                $seedSource = (string) ($user->email ?: $user->full_name ?: 'staff');
                $user->username = static::generateUniqueUsername($seedSource);
            }

            if (empty($user->role)) {
                $user->role = 'Staff Member';
            }
        });
    }

    /**
     * Map Breeze's conventional `name` attribute directly to the POS `full_name` column.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (): string => (string) ($this->attributes['full_name'] ?? ''),
            set: fn (?string $value): array => ['full_name' => (string) ($value ?? '')],
        );
    }

    /**
     * Generate a unique POS username from a name or email prefix.
     */
    public static function generateUniqueUsername(string $source): string
    {
        $prefix = Str::before(trim($source), '@');
        $base = preg_replace('/[^a-zA-Z0-9._-]+/', '.', strtolower($prefix)) ?? 'staff';
        $base = trim($base, '._-');
        $base = $base !== '' ? substr($base, 0, 40) : 'staff';

        $candidate = $base;
        $counter = 1;

        while (static::query()->where('username', $candidate)->exists()) {
            $candidate = substr($base, 0, 40).'.'.$counter;
            $counter++;
        }

        return $candidate;
    }

    /**
     * Sales transactions recorded by this staff member.
     *
     * @return HasMany<Sale, $this>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'sold_by');
    }

    /**
     * Determine whether the user has an existing avatar stored on the public disk.
     */
    public function hasAvatar(): bool
    {
        return ! empty($this->avatar) && Storage::disk('public')->exists($this->avatar);
    }

    /**
     * Resolve the public image URL for this user's avatar, falling back to the placeholder asset.
     */
    public function avatarUrl(): string
    {
        if ($this->hasAvatar()) {
            return is_file(public_path('storage/'.$this->avatar))
                ? '/storage/'.ltrim($this->avatar, '/')
                : route('media.show', ['folder' => 'avatars', 'filename' => basename($this->avatar)], false);
        }

        return '/assets/images/avatar-placeholder.svg';
    }

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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
