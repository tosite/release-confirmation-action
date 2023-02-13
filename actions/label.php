<?php

echo "add label.";
$keys = [
    'endpoint',
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
var_dump($params, getenv('GITHUB_TOKEN'));
//$cli = new GithubClient($params['base_url']);
//$cli->addLabels($params['merged_label'], $params['merged_color']);
