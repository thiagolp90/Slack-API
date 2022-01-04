<?php

namespace Developes\Slack\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlackNotification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['slack_id', 'message', 'sended_at'];
}
