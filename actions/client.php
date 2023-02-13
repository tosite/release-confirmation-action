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
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            "Authorization: token " . getenv('GITHUB_TOKEN'),
            "Content-Type: application/json"
        ]);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    }

    protected function setUrl($uri)
    {
        $url = $this->url . "/{$this->repo}/{$uri}";
        return empty($this->queryParams) ? $url : $url . '?' . http_build_query($this->queryParams);
    }

    public function addLabels($name, $color)
    {
        $this->setAuth();
        $url = $this->setUrl("issues/{$this->number}/labels");
        if ($this->isDebug) {
            var_dump(['url' => $url]);
        }
        $params = [
            'name'  => $name,
            'color' => $color,
        ];
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_exec($this->curl);
        if (curl_errno($this->curl)) {
            die('Error:' . curl_error($this->curl));
        }
        echo "[success]added label.\n";
        curl_close($this->curl);
    }

    public function removeLabels()
    {

    }

    public function fetchClosedPulls()
    {

    }
}
