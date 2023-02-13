<?php

class GithubClient
{
    protected $curl;
    protected $url;
    protected $queryParams;

    public function __construct($baseUrl, $queryParams = [])
    {
        $this->url = $baseUrl;
        $this->queryParams = $queryParams;
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

    protected function setUrl()
    {
        return empty($this->queryParams) ? $this->url : $this->queryParams . '?' . http_build_query($this->queryParams);
    }

    public function addLabels($name, $color)
    {
        $this->setAuth();
        $params = [
            'name'  => $name,
            'color' => $color,
        ];
        curl_setopt($this->curl, CURLOPT_URL, $this->setUrl());
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_exec($this->curl);
        if (curl_errno($this->curl)) {
            die('Error:' . curl_error($this->curl));
        }
        echo "[success]added label.";
        curl_close($this->curl);
    }

    public function removeLabels()
    {

    }

    public function fetchClosedPulls()
    {

    }
}
