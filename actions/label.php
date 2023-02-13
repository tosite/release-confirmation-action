<?php

echo "add label.";
$keys = [
    'endpoint',
    'github_token',
    'action',
    'base_url',
    'repo',
    'number',
    'merged_label',
    'released_label',
    'merged_color',
    'released_color',
];
$params = array_combine($keys, $argv);
var_dump($params, $params['github_token']);
//$cli = new GithubClient($params['base_url']);
//$cli->addLabels($params['merged_label'], $params['merged_color']);
