<?php

class SlackClient
{
    protected $url;
    protected $isDebug = false;
    protected $webhookUrl;
    protected $mention;
    protected $subject;
    protected $attachments = [];

    public function __construct()
    {
        $this->webhookUrl = getenv('SLACK_WEBHOOK');
        if (empty($this->webhookUrl)) {
            throw new \Exception('undefined SLACK_WEBHOOK env.');
        }
    }

    public function debugMode()
    {
        $this->isDebug = true;
    }

    public function setMention($mention)
    {
        $this->mention = $mention;
        return $this;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    protected function buildMessage($repo, $pulls)
    {
        $message = "*:open_file_folder:Repository: {$repo}*\n";
        foreach ($pulls as $pull) {
            $message .= "<{$pull['html_url']}|{$pull['title']}> by {$pull['user']}\n";
        }
        return $message;
    }

    protected function buildAttachmentParams($repoPulls, $title, $color)
    {
        $message = '';
        if ($this->isDebug) {
            echo "[DEBUG]show repositories:\n";
            var_dump($repoPulls);
        }
        foreach ($repoPulls as $repo => $pulls) {
            if (empty($pulls)) {
                continue;
            }
            $message .= $this->buildMessage($repo, $pulls);
        }
        if (empty($message)) {
            return;
        }
        $this->attachments = array_merge(
            [
                [
                    'title' => $title,
                    'text'  => $message,
                    'color' => $color,
                ]
            ],
            $this->attachments);
    }

    public function setUnreleasedParams($title, $repoPulls)
    {
        $this->buildAttachmentParams($repoPulls, ":warning:{$title}", '#bf1e56');
    }

    public function setReleasedParams($title, $repoPulls)
    {
        $this->buildAttachmentParams($repoPulls, ":white_check_mark:{$title}", '#a4c520');
    }

    protected function buildAttachmentMessage()
    {
        $mention = !empty($this->mention) ? "{$this->mention} " : '';
        $params = [
            'text'        => "{$mention}*{$this->subject}*",
            'attachments' => $this->attachments,
        ];
        if ($this->isDebug) {
            echo "[DEBUG]show attachment:\n";
            var_dump($params);
        }
        return $params;
    }

    public function send()
    {
        if (empty($this->attachments)) {
            echo "[INFO]nothing to be notified.";
            return;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->webhookUrl);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->buildAttachmentMessage()));
        $result = curl_exec($curl);
        if ($this->isDebug) {
            echo "[DEBUG]show response:\n";
            var_dump($result);
        }
        curl_close($curl);
    }
}
