<?php
$config = [
    'channelId' => 'LINE_CHANNEL_ID',
    'channelSecret' => 'LINE_CHANNEL_SECRET',
    'channelMid' => 'LINE_CHANNEL_MID',
];
$bot = new LINEBot($config, new GuzzleHTTPClient($config));

?>
