<?php
require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app->post('/callback', function (Request $request) use ($app) {
    $client = new GuzzleHttp\Client();
    $body = json_decode($request->getContent(), true);
    foreach ($body['result'] as $msg) {
        $reply_message = chat($msg['content']['text']);

        $resContent = $msg['content'];
        $resContent['text'] = $reply_message;
        $requestOptions = [
            'body' => json_encode([
                'to' => [$msg['content']['from']],
                'toChannel' => 1383378250, #Fixed value
                'eventType' => '138311608800106203', #Fixed value
                'content' => $resContent,
            ]),
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'X-Line-ChannelID' => getenv('LINE_CHANNEL_ID'),
                'X-Line-ChannelSecret' => getenv('LINE_CHANNEL_SECRET'),
                'X-Line-Trusted-User-With-ACL' => getenv('LINE_CHANNEL_MID'),
            ],
            'proxy' => [
                'https' => getenv('FIXIE_URL'),
            ],
        ];
        try {
            $client->request('post', 'https://trialbot-api.line.me/v1/events', $requestOptions);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
    return 'OK';
});

$app->run();

function chat($send_message) {
    // userlocal API
    $api_key= 'USERLOCAL_API_KEY';
    $api_url = sprintf('https://chatbot-api.userlocal.jp/api/chat?key=%s', $api_key);
    $req_body = array('utt' => $text);
    $req_body['context'] = $send_message;

    $headers = array(
        'Content-Type: application/json; charset=UTF-8',
    );
    $options = array(
        'http'=>array(
            'method'  => 'POST',
            'header'  => implode("\r\n", $headers),
            'content' => json_encode($req_body),
            )
        );
    $stream = stream_context_create($options);
    $res = json_decode(file_get_contents($api_url, false, $stream));

    return $res->utt;
}
