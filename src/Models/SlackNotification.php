<?php

namespace Developes\Slack\Models;

use Developes\Slack\Models\SlackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlackNotification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['slack_id', 'message', 'with_confirm_buttons', 'confirmed', 'sended_at', 'confirmed_at'];

    protected $dates = ['sended_at', 'confirmed_at'];

    /**
     * Relationship with slack_users table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(SlackUser::class, 'slack_id', 'slack_id');
    }
}
