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
        $checkSent = true;
        if(!$this->delayed){
            $this->timeToDelay = now();
            try {
                $data = [
                    'channel' => $to,
                    'text' => $message
                ];
                $this->request('chat.postMessage', $data);
            } catch (\Throwable $th) {
                $checkSent = false;
            }
        }
        $data = [
            'sended_at' => $this->timeToDelay,
            'message' => $message,
            'slack_id' => $to
        ];
        $slack = SlackNotification::create($data);
        if($checkSent){
            $slack->delete();
        }
    }

    /**
     * Get the user identity
     * @param string $slackId
     * @return array
     */
    public function getUserIdentity($slackId){
        try {
            $data = ['user' => $slackId];
            $result = $this->request('users.identity', $data, 'GET');
        } catch (\Throwable $th) {
            return ['success' => false];
        }

        return json_decode($result, true);
    }

    /**
     * Get a list of users
     * @return array
     */
    public function getUsersList(){
        try {
            $result = $this->request('users.identity', [], 'GET');
        } catch (\Throwable $th) {
            return ['success' => false];
        }

        return json_decode($result, true);
    }

    /**
     * Send the request to API Slack
     * @param string $endpoint
     * @param array $message
     * @param string $verb
     */
    public function request(string $endpoint, array $data = [], string $verb = 'POST'){
        $default = [
            'token' => config('slack.token'),
            'username' => config('slack.username')
        ];

        $data = array_merge($default, $data);

        if($verb == 'POST'){
            $response = Http::asForm()->post('https://slack.com/api/'.$endpoint, $data);
        }else{
            $response = Http::asForm()->get('https://slack.com/api/'.$endpoint, $data);
        }

        return $response;
    }
}
