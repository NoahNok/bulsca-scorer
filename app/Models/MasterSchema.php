<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSchema extends Model
{
    use HasFactory;

    protected $casts = [
        'schema' => 'array',
    ];

    public function getCompetition()
    {
        return $this->hasOne(Competition::class, 'id', 'competition');
    }

    /**
     * @return Collection<int, ResultSchema>
     */
    public function getSheets(): Collection
    {

        $sheetIds = array_map(fn($sheet) => $sheet['id'], $this->schema['sheets'] ?? []);
        $sheetWeights = array_column($this->schema['sheets'] ?? [], 'weight', 'id');
        $sheets = ResultSchema::whereIn('id', $sheetIds)->get();

        $sheets->each(function ($sheet) use ($sheetWeights) {
            $sheet->weight = $sheetWeights[$sheet->id];
        });

        return $sheets;
    }
}
