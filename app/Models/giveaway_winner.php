<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class giveaway_winner extends Model
{
    use HasFactory;
    protected $table = 'giveaway_winners';
    public function scopeRecentLastWeek($query)
    {
        // Calculate the date for one week ago from the current date
        $oneWeekAgo = Carbon::now()->subWeek();
        return $query->whereBetween('created_at', [$oneWeekAgo, Carbon::now()])
            ->orderByDesc('created_at')
            ->limit(8);
    }
}
