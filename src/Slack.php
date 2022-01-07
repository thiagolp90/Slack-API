<?php

namespace Developes\Slack;

use Carbon\Carbon;
use Developes\Slack\Models\SlackNotification;
use Illuminate\Support\Facades\Http;

class Slack
{
    public $delayed = false;
    public $timeToDelay;

    public function __construct()
    {
        $this->timeToDelay = now();
    }

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
        $data = [
            'sended_at' => $this->timeToDelay,
            'message' => $message,
            'slack_id' => $to
        ];
        $slack = SlackNotification::create($data);
        if(!$this->delayed){
            try {
                $data = [
                    'channel' => $to,
                    'text' => $message
                ];
                return $this->request('chat.postMessage', $data);
            } catch (\Throwable $th) {
                $checkSent = false;
                return ['success' => false];
            }
            if($checkSent){
                $slack->delete();
            }
        }
    }

    /**
     * Send a slack message with confirm buttons
     * @param string $to
     * @param string $message
     * @param array $buttons
     */
    public function sendMessageWithConfirmButtons(string $to, string $message, array $buttons = ['Yes', 'No'])
    {
        $checkSent = true;
        $data = [
            'sended_at' => $this->timeToDelay,
            'message' => $message,
            'with_confirm_buttons' => true,
            'slack_id' => $to
        ];
        $slack = SlackNotification::create($data);

        if(!$this->delayed){
            try {
                $blocks = json_encode([
                    [
                        "type" => "section",
                        "text" => [
                            "type" => "mrkdwn",
                            "text" => $message
                        ]
                    ],
                    [
                        "type" => "actions",
                        "block_id" => "$slack->id",
                        "elements" => [
                            [
                                "type" => "button",
                                "text" => [
                                    "type" => "plain_text",
                                    "text" => $buttons[0],
                                    "emoji" => false
                                ],
                                "style" => "primary",
                                "value" => "1"
                            ],
                            [
                                "type" => "button",
                                "text" => [
                                    "type" => "plain_text",
                                    "text" => $buttons[1],
                                    "emoji" => false
                                ],
                                "style" => "danger",
                                "value" => "0"
                            ],
                        ]
                    ]
                ]);
                $data = [
                    'channel' => $to,
                    'text' => $message,
                    'blocks' => $blocks
                ];
                return $this->request('chat.postMessage', $data);
            } catch (\Throwable $th) {
                $checkSent = false;
                return ['success' => false];
            }

            if($checkSent){
                $slack->delete();
            }
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
            return $this->request('users.identity', $data, 'GET');
        } catch (\Throwable $th) {
            return ['success' => false];
        }
    }

    /**
     * Get a list of users
     * @return array
     */
    public function getUsersList()
    {
        try {
            return $this->request('users.identity', [], 'GET');
        } catch (\Throwable $th) {
            return ['success' => false];
        }
    }

    /**
     * Send callback to Slack Webhook
     * @param string $url
     * @param array $message
     * @return array
     */
    public function callback($url, $message)
    {
        $data = [
            'replace_original' => true,
            'text' => $message
        ];
        $response = Http::asForm()->post($url, $data);

        return json_decode($response->body(), true);
    }

    /**
     * Send the request to API Slack
     * @param string $endpoint
     * @param array $message
     * @param string $verb
     * @return array
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

        return json_decode($response->body(), true);
    }
}
