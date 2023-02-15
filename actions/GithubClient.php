<?php

class GithubClient
{
    protected $curl;
    protected $url;
    protected $isDebug = false;
    protected $repo;
    protected $number;
    protected $pulls;

    public function __construct($baseUrl)
    {
        $this->url = $baseUrl;
        $this->curl = curl_init();
        $this->setAuth();
    }

    public function setRepo($repo)
    {
        $this->repo = $repo;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setDebugMode()
    {
        $this->isDebug = true;
    }

    public function setAuth()
    {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            "Authorization: token " . getenv('GITHUB_TOKEN'),
            "Content-Type: application/json",
            "User-Agent: release-confirmation-action",
        ]);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    }

    protected function setUrl($uri, $options = [])
    {
        $url = $this->url . "/{$this->repo}/{$uri}";
        return empty($options) ? $url : $url . '?' . http_build_query($options);
    }

    public function addLabels($label)
    {
        $url = $this->setUrl("issues/{$this->number}/labels");
        if ($this->isDebug) {
            var_dump(['url' => $url]);
        }
        $params = [$label];
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        $res = curl_exec($this->curl);
        if ($this->isDebug) {
            var_dump($res);
        }
        var_dump(curl_errno($this->curl));
        if (curl_errno($this->curl)) {
            die('Error:' . curl_error($this->curl));
        }
        curl_close($this->curl);
    }

    public function removeLabel($label)
    {
        $url = $this->setUrl("issues/{$this->number}/labels/{$label}");
        if ($this->isDebug) {
            var_dump(['url' => $url]);
        }
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $res = curl_exec($this->curl);
        if ($this->isDebug) {
            var_dump($res);
        }
        if (curl_errno($this->curl)) {
            die('Error:' . curl_error($this->curl));
        }
        var_dump(curl_errno($this->curl));
        curl_close($this->curl);
    }

    public function fetchPulls($options)
    {
        $url = $this->setUrl("pulls", $options);
        if ($this->isDebug) {
            var_dump(['url' => $url]);
        }
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $res = curl_exec($this->curl);
        var_dump(curl_errno($this->curl));
        if (curl_errno($this->curl)) {
            die('Error:' . curl_error($this->curl));
        }
        curl_close($this->curl);

        $this->pulls = json_decode($res, true);
    }

    public function filteringUnreleasedPulls($mergedLabel)
    {
        $notifies = [];
        foreach ($this->pulls as $pull) {
            if ($this->isDebug) {
                var_dump("{$pull['html_url']} - {$pull['title']}");
            }
            foreach ($pull['labels'] as $label) {
                if ($label['name'] === $mergedLabel) {
                    $notifies[] = $pull;
                }
            }
        }
        return $notifies;
    }

    public function filteringReleasedPulls($releasedLabel, $term)
    {
        $notifies = [];
        foreach ($this->pulls as $pull) {
            if ($this->isDebug) {
                var_dump("{$pull['html_url']} - {$pull['title']}");
            }
            $mergedAt = strtotime($pull['merged_at']);
            if (time() - $mergedAt >= $term * 24 * 60 * 60) {
                continue;
            }
            foreach ($pull['labels'] as $label) {
                if ($label['name'] === $releasedLabel) {
                    $notifies[] = $pull;
                }
            }
        }
        return $notifies;

    }
}
