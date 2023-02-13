<?php
$isDebug = (int)getenv('DEBUG_MODE') === 1;
$keys = [
    'endpoint',
    'action',
    'base_url',
    'repo',
    'number',
    'merged_label',
    'released_label',
];
if ($isDebug) {
    var_dump(['key' => $keys, 'values' => $argv]);
}
$params = array_combine($keys, $argv);
