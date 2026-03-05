<?php

namespace App\Models\Activity;

use App\Models\AbstractClasses\Entity;
use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use App\Models\SERC;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'related_id',
        'related_type',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function render()
    {
        $related = $this->related;


        return match (true) {
            $related instanceof Competition => view('components.activity-log.competition', ['name' => $related->name, 'org' => $related->getOrganisation, 'link' => route('comps.view', $related)]),
            $related instanceof Event => view('components.activity-log.event', ['name' => $related->getName(), 'is_serc' => $related instanceof SERC, 'event' => $related]),
            $related instanceof Entity => view('components.activity-log.entity', ['name' => $related->getName(), 'type' => class_basename($related)]),


            default => 'Related Item Deleted',
        };



        // Add more cases for other related types if needed
        return 'Unknown related type';
    }
}
