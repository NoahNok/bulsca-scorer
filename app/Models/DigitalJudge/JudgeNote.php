<?php

namespace App\Models\DigitalJudge;

use App\Models\CompetitionTeam;
use App\Models\SERCJudge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgeNote extends Model
{
    use HasFactory;

    protected $table = "digitaljudge_judge_notes";

    protected $fillable = [
        'judge',
        'entity_type',
        'entity_id'
    ];

    public function getJudge()
    {
        return $this->hasOne(SERCJudge::class, 'id', 'judge');
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
