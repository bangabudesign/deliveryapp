<?php
namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        $content = $notification->toWhatsApp($notifiable);
        
        $this->message($notifiable, $content);
    }

    public function message($notifiable, $content)
    {
        $api_url = "https://conversations.messagebird.com/v1/send";
        $access_key = config('services.messagebird.access_key');
        $channel_id = config('services.messagebird.channel_id');
        $namespace_id = config('services.messagebird.namespace_id');
        $number = '+62'.$notifiable->routeNotificationForWhatsApp();
  
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'AccessKey '.$access_key,
        ];
        
        if($content->type == 'media_template') {
            $components = [];
            $header_parameters = [];
            $url = ["url" => $content->image];
            $header_parameters[] = ["type" => "image", "image" => $url];
            $components[] = ["type" => "header", "parameters" => $header_parameters];
            $components[] = ["type" => "body", "parameters" => $content->parameters];
            $data = [
                "type" => "hsm",
                "to" => $number,
                "from" => $channel_id,
                "content" => [
                    "hsm" => [
                        "namespace" => $namespace_id,
                        "templateName"=> $content->templateName,
                        "language" => [
                            "policy" => "deterministic",
                            "code" => "id"
                        ],
                        "components" => $components
                    ]
                ]
            ];
        } else {
            $data = [
                "type" => "hsm",
                "to" => $number,
                "from" => $channel_id,
                "content" => [
                    "hsm" => [
                        "namespace" => $namespace_id,
                        "templateName"=> $content->templateName,
                        "language" => [
                            "policy" => "deterministic",
                            "code" => "id"
                        ],
                        "params" => $content->params
                    ]
                ]
            ];
        }

        $response = Http::withHeaders($headers)->post($api_url, $data);
  
        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
    }
}