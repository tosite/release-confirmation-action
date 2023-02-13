<?php

class SlackClient
{
    protected $curl;
    protected $url;
    protected $isDebug = false;
    protected $repo;
    protected $number;

    public function __construct()
    {
    }

    public function debugMode()
    {
        $this->isDebug = true;
    }

    public function send($pulls)
    {
        $data = "payload=" . json_encode(["text" => $this->buildMessage($pulls)]);
        if ($this->isDebug) {
            var_dump($data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, getenv('SLACK_WEBHOOK'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($this->isDebug) {
            var_dump($result);
        }
        curl_close($ch);
    }

    protected function buildMessage($pulls)
    {
        $message = '';
        $mention = getenv('MENTION');
        $subject = getenv('SUBJECT');
        if ($mention !== 'NULL') {
            $message .= "$mention\n";
        }
        $message .= "**{$subject}**\n";
        foreach ($pulls as $pull) {
            $message .= "- <{$pull['html_url']}|{$pull['title']}> by {$pull['user']['login']}\n";
        }
        return $message;
    }
}
