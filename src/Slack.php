<?php

namespace Developes\Slack;

use Carbon\Carbon;
use Developes\Slack\Models\SlackNotification;
use Illuminate\Support\Facades\Http;

class Slack
{
    public $delayed = false;
    public $timeToDelay;

    /**
     * Add a delay to send
     * @param int $time
     * @param string $type ['minutes', 'hours', 'days', 'months']
     * @return $this
     */
    public function delayTo(int $time, string $type = 'days')
    {
        $this->delayed = true;

        $delay = now();
        if($type == 'minutes'){
            $delay = $delay->addMinutes($time);
        }elseif($type == 'hours'){
            $delay = $delay->addHours($time);
        }elseif($type == 'months'){
            $delay = $delay->addMonths($time);
        }else{
            $delay = $delay->addDays($time);
        }
        $this->timeToDelay = $delay->startOfDay();

        return $this;
    }

    /**
     * Set a time to send
     * @param Carbon $time
     * @return $this
     */
    public function sendAt(Carbon $time)
    {
        $this->delayed = true;
        $this->timeToDelay = $time;
        return $this;
    }

    /**
     * Send a slack message
     * @param string $to
     * @param string $message
     */
    public function sendMessage(string $to, string $message){
        if(!$this->delayed){
            dd(config('app.slack.token'));
            $data = [
                'token' => config('app.slack.token'),
                'channel' => $to,
                'text' => $message,
                'username' => config('app.slack.username')
            ];

            Http::asForm()->post('https://slack.com/api/chat.postMessage', $data);

            $this->timeToDelay = now();
        }

        $data = [
            'sended_at' => $this->timeToDelay,
            'message' => $message,
            'slack_id' => $to
        ];
        SlackNotification::insert($data);
    }
}
