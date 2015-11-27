<?php

/**
 * Tour de France Bot for Slack.
 *
 * It uses the unofficial Libération json API.
 *
 * You will need a token from Slack.
 * Jump at https://api.slack.com/ under the "Authentication" part and you will find your token.
 *
 * @author j0k <jeremy.benoist@gmail.com>
 * @license MIT
 */

/**
 * All the configuration are just below
 */
include('./Html2Text.php');

// Slack stuff
const SLACK_TOKEN = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';
const SLACK_CHANNEL = '#liberation-live';
//Libération flux id
const LIBE_FLUX = '';

function postToSlack($text, $title, $attachments_text = '', $pretty = true, $avatar_url, $author_name, $thumb = '')
{
    $html = new \Html2Text\Html2Text($attachments_text);
    $attachments_text = $html->getText();
    $attachments_text = str_replace("\n", " ", $attachments_text);
    $attachments_text = str_replace('"', '\"', $attachments_text);

    $slackUrl = 'https://slack.com/api/chat.postMessage?token=' . SLACK_TOKEN .
        '&channel=' . urlencode(SLACK_CHANNEL) .
        '&username=' . urlencode($author_name) .
        '&icon_url=' . urlencode($avatar_url) .
        '&text=' . urlencode($text);

    if ($pretty) {
        $slackUrl .= '&unfurl_links=1&parse=full&pretty=1';
    }

    if ($attachments_text) {
        $slackUrl .= '&attachments=' . urlencode('[{"title": "'. $title .'",  "text": "' . $attachments_text . '", "image_url": "'. $thumb .'"}]');
    }
    file_get_contents($slackUrl);
}

$dbFile = './LiberationLiveDB.json';

$db = json_decode(file_get_contents($dbFile), true);

$response = file_get_contents('http://www.liberation.fr/api/v3/live/?flux='.LIBE_FLUX.'&v=beta&ajax&since='.$db['last_update']);
$response = json_decode($response);

if (!$response) {
    var_dump('feed not ready');
    die();
}

var_dump('processing response');


foreach ($response->results as $post) {
    var_dump('processing event');

    //Event
    $event = ':loudspeaker:';
    //Extra space for emoji
    $event .= $event ? ' ' : '';
    //Thumbnail
    if(isset($post->asset->data->entities->media[0]->media_url)) {
        $thumb = $post->asset->data->entities->media[0]->media_url;
    } elseif(isset($post->asset->data->url)) {
        $thumb = $post->asset->data->url;

    } else {
        $thumb = '';
    }

    //Post to slack
    postToSlack(
        $event . 'Live - '. date('h:i'),
        $post->flavor_text . ' ' . $post->title, $post->content,
        true,
        'https://pbs.twimg.com/profile_images/559638245303005184/Z-sZEX4e_400x400.jpeg',
        'Libération',
        $thumb
    );
}

//Log last update time
sleep(2); //avoid duplicate
$now = microtime(true);
$db['last_update'] = $now;
file_put_contents($dbFile, json_encode($db));
