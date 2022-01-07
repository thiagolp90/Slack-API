<?php

namespace Developes\Slack\Http\Controllers;

use Developes\Slack\Facades\Slack;
use Developes\Slack\Models\SlackNotification;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Request;

class Controller extends BaseController
{
    /**
     * Method to trait the slack webhook call
     * @param Request $request
     */
    public function webhook(Request $request)
    {
        $payload = json_decode($request['payload'], true);

        if(isset($payload) && $payload != "") {
            $response_url = $payload['response_url'];
            $button = $payload['actions'][0]['value'];
            $id = $payload['actions'][0]['block_id'];

            $slack = SlackNotification::find($id);
            $data = [
                'confirmed' => $button,
                'confirmed_at' => now()
            ];
            $slack->update($data);

            Slack::callback($response_url, $button);
        }
    }
}
