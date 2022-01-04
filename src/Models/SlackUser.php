<?php

namespace Developes\Slack\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlackNotification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'slack_id', 'name'];

    /**
     * Relationship with users table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
