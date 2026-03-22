<?php

namespace App\Models;

use App\Models\Organisation\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DQCode extends Model
{
    use HasFactory;

    protected $table = "dq_codes";

    static function message($code, ?Organisation $organisation = null)
    {

        if (str_starts_with(strtolower($code), 'dq')) $code = substr($code, 2);

        $query = DQCode::where('code', $code);

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
        return "DQ{$this->code}";
    }
}
