<?php

namespace App\Models\Brands;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'website',
        'email',
        'primary_color',
        'secondary_color',
        'logo',
    ];

    public function getLogo(): string
    {
        return asset('storage/' . $this->logo);
    }

    public function getUsers()
    {
        return $this->belongsToMany(User::class, 'brand_users', 'brand', 'user')->withPivot('role');
    }

    public function attachUser(User $user, string $role = 'admin')
    {
        $this->getUsers()->attach($user, ['role' => $role]);
    }

    public function detachUser(User $user)
    {
        $this->getUsers()->detach($user);
    }

    public function isBrandRole(User $user, string|array $role)
    {


        if ($role == '*' || $role == ['*']) {
            $role = ['admin', 'welfare'];
        }

        if ($user->admin) return true;

        $brand = $user->getBrands()->where('brand', $this->id)->first();


        if (!$brand) return false;


        if (is_array($role)) {
            foreach ($role as $r) {
                if ($brand->pivot->role == $r) {
                    return true;
                }
            }
            return false;
        } else {

            return $brand->pivot->role == $role;
        }
    }

    public function getCompetitions()
    {
        return $this->hasMany(Competition::class, 'brand', 'id');
    }
}
