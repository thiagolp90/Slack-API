<?php

namespace Developes\Slack\Jobs;

use Developes\Slack\Facades\Slack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Developes\Slack\Models\SlackNotification;

class SendSlackNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notifications = SlackNotification::where('sended_at', '<=', now())->get();

        foreach($notifications as $notification){
            Slack::sendMessage($notification->slack_id, $notification->message);
            $notification->delete();
        }
    }
}
