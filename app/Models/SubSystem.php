<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSystem extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->sites->isEmpty()) {
            return 0;
        }

        $totalPercentage = 0;
        foreach ($this->sites as $site) {
            $totalPercentage += $site->progress_percentage;
        }

        return round($totalPercentage / $this->sites->count());
    }
}