<?php

namespace App\Models;

use App\Models\Organisation\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenaltyCode extends Model
{
    use HasFactory;


    static function message($code, ?Organisation $organisation = null)
    {

        if (str_starts_with(strtolower($code), 'p')) $code = substr($code, 1);

        $query = PenaltyCode::where('code', $code);

        if ($organisation) {
            $query = $query->where('organisation', $organisation->id);
        }

        return $query->first()?->description ?: "";
    }

    public function eventCodes()
    {
        return $this->morphMany(EventCode::class, 'pendq');
    }

    public function __toString(): string
    {
        return "P{$this->code}";
    }
}
