<?php

namespace Developes\Slack\Models;

use App\Models\User;
use Developes\Slack\Models\SlackNotification as ModelsSlackNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlackUser extends Model
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

    /**
     * Relationship with slack_notifications table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notifications()
    {
        return $this->hasMany(ModelsSlackNotification::class, 'slack_id', 'slack_id');
    }
}
