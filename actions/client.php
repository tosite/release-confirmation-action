<?php

class GithubClient
{
    protected $curl;
    protected $url;
    protected $queryParams;
    protected $isDebug = false;
    protected $repo;
    protected $number;

    public function __construct($baseUrl, $queryParams = [])
    {
        $this->url = $baseUrl;
        $this->queryParams = $queryParams;
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

    protected function setUrl($uri)
    {
        $url = $this->url . "/{$this->repo}/{$uri}";
        return empty($this->queryParams) ? $url : $url . '?' . http_build_query($this->queryParams);
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

    public function fetchClosedPulls()
    {

    }
}
