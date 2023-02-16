<?php

class GithubClient
{
    protected $url;
    protected $isDebug = false;
    protected $repo;
    protected $number;
    protected $pulls;

    public function __construct($baseUrl)
    {
        $this->url = $baseUrl;
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

    public function setAuth($curl)
    {
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: token " . getenv('GITHUB_TOKEN'),
            "Content-Type: application/json",
            "User-Agent: release-confirmation-action",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    }

    protected function setUrl($uri, $options = [])
    {
        $url = $this->url . "/{$this->repo}/{$uri}";
        return empty($options) ? $url : $url . '?' . http_build_query($options);
    }

    public function addLabels($label)
    {
        $curl = curl_init();
        $this->setAuth($curl);
        $url = $this->setUrl("issues/{$this->number}/labels");
        if ($this->isDebug) {
            echo "[DEBUG]show url:\n";
            var_dump(['url' => $url]);
        }
        $params = [$label];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        $res = curl_exec($curl);
        if ($this->isDebug) {
            echo "[DEBUG]show response:\n";
            var_dump($res);
        }
        var_dump(curl_errno($curl));
        if (curl_errno($curl)) {
            die('Error:' . curl_error($curl));
        }
        curl_close($curl);
    }

    public function removeLabel($label)
    {
        $curl = curl_init();
        $this->setAuth($curl);
        $url = $this->setUrl("issues/{$this->number}/labels/{$label}");
        if ($this->isDebug) {
            echo "[DEBUG]show url:\n";
            var_dump(['url' => $url]);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $res = curl_exec($curl);
        if ($this->isDebug) {
            echo "[DEBUG]show response:\n";
            var_dump($res);
        }
        if (curl_errno($curl)) {
            die('Error:' . curl_error($curl));
        }
        var_dump(curl_errno($curl));
        curl_close($curl);
    }

    public function fetchPulls($options)
    {
        $curl = curl_init();
        $this->setAuth($curl);
        $url = $this->setUrl("pulls", $options);
        if ($this->isDebug) {
            echo "[DEBUG]show url:\n";
            var_dump(['url' => $url]);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        var_dump(curl_errno($curl));
        if (curl_errno($curl)) {
            die('Error:' . curl_error($curl));
        }
        curl_close($curl);

        $this->pulls = json_decode($res, true);
    }

    public function filteringUnreleasedPulls($mergedLabel)
    {
        $notifies = [];
        foreach ($this->pulls as $pull) {
            if ($this->isDebug) {
                echo "[DEBUG]show link:\n";
                var_dump("{$pull['html_url']} - {$pull['title']}");
            }
            foreach ($pull['labels'] as $label) {
                if ($label['name'] === $mergedLabel) {
                    $notifies[] = [
                        'html_url' => $pull['html_url'],
                        'title'    => $pull['title'],
                        'user'     => $pull['user']['login'],
                    ];
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
                echo "[DEBUG]show link:\n";
                var_dump("{$pull['html_url']} - {$pull['title']}");
            }
            $mergedAt = strtotime($pull['merged_at']);
            if (time() - $mergedAt >= $term * 24 * 60 * 60) {
                continue;
            }
            foreach ($pull['labels'] as $label) {
                if ($label['name'] === $releasedLabel) {
                    $notifies[] = [
                        'html_url' => $pull['html_url'],
                        'title'    => $pull['title'],
                        'user'     => $pull['user']['login'],
                    ];
                }
            }
        }
        return $notifies;
    }
}
