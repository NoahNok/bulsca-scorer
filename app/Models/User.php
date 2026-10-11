<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\Organisation\Organisation;
use App\Models\Organisation\OrganisationUserAccess;
use ALajusticia\Logins\Traits\HasLogins;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\OneTimePasswords\Models\Concerns\HasOneTimePasswords;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions, HasOneTimePasswords, HasLogins;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'judge_only',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'judge_only' => 'boolean',
    ];

    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * Self-registered officials, who can only use Digital Judge, not the main site.
     */
    public function isJudgeOnly(): bool
    {
        return (bool) $this->judge_only;
    }

    /**
     * Sign out every other device, leaving the one making this request signed in.
     */
    public function logoutOtherDevices(): void
    {
        $this->revokeLogins(
            $this->logins()->where(fn ($query) => $query
                ->where('session_id', '!=', session()->getId())
                ->orWhereNull('session_id'))
        );
    }

    /**
     * Sign out every device, including this one.
     */
    public function logoutEverywhere(): void
    {
        $this->revokeLogins($this->logins());
    }

    /**
     * Destroy the session behind each login and delete it. Used instead of the package's
     * revoke(), which deletes nothing when none of the logins has a session id, and with
     * withExpired() so a login past its expiry whose session is still live isn't missed.
     */
    private function revokeLogins($query): void
    {
        foreach ($query->withExpired()->get() as $login) {
            $login->revoke() || $login->delete();
        }
    }


    public function getWhatIfEditors()
    {
        return $this->hasMany(Competition::class, 'wi_user', 'id');
    }

    public function getCompetitionsByAccess($access)
    {
        return $this->hasManyThrough(
            Competition::class,
            UserCompetitionAccess::class,
            'user',
            'id',
            'id',
            'competition'
        )->where('access_to', $access)->distinct('competitions.id')->orderBy('when', 'desc');
    }

    public function getCompetitionsWithAccess()
    {
        return $this->hasManyThrough(
            Competition::class,
            UserCompetitionAccess::class,
            'user',
            'id',
            'id',
            'competition'
        )->where('access_to', '!=', 'none')->distinct('competitions.id')->orderBy('when', 'desc');
    }

    public function getOrganisations()
    {
        return $this->hasManyThrough(
            Organisation::class,
            OrganisationUserAccess::class,
            'user',
            'id',
            'id',
            'organisation'
        )->where('access_to', '!=', 'none')->distinct('organisations.id');
    }


    public function officiating()
    {
        return $this->belongsToMany(Competition::class, 'competition_officials', 'user_id', 'competition_id');
    }

    public function violations(?Competition $competition = null)
    {
        $query = $this->hasMany(JudgeDQSubmission::class, 'user_id', 'id');

        if ($competition) {
            $query->where('competition', $competition->id);
        }

        return $query;
    }
}
