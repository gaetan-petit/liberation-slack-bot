<?php

/**
 * Tour de France Bot for Slack.
 *
 * It uses the unofficial letour.fr json API (the one used for their mobile app iOS/Android).
 * It will post a message :
 *   - when a stage will start (with info about it + map)
 *   - every telegrams from the feed (could be too verbose..)
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

// Slack stuff
const SLACK_TOKEN      = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';
const SLACK_CHANNEL    = '#Le-Monde-live';
const SLACK_BOT_NAME   = 'Le Monde';
const SLACK_BOT_AVATAR = 'http://cdnmo.coveritlive.com/media/avitars/phpFpVVqziconelemondefrgrise1.jpg';

function postToSlack($text, $attachments_text = '', $pretty = true, $avatar_url, $author_name)
{
  $attachments_text = str_replace("\n", "", $attachments_text);
  $attachments_text = str_replace('"', '\"', $attachments_text);

  $slackUrl = 'https://slack.com/api/chat.postMessage?token='.SLACK_TOKEN.
    '&channel='.urlencode(SLACK_CHANNEL).
    '&username='.urlencode(SLACK_BOT_NAME).
    '&icon_url='.$avatar_url.
    '&text='.urlencode($text);

  if ($pretty)
  {
    $slackUrl .= '&unfurl_links=1&parse=full&pretty=1';
  }

  if ($attachments_text)
  {
    $slackUrl .= '&attachments='.urlencode('[{"text": "'.$attachments_text.'"}]');
  }

  var_dump(getUrl($slackUrl));
}

$dbFile = './LeMondeLiveDB.json';

$db = json_decode(file_get_contents($dbFile), true);
$response = file_get_contents('http://live.lemde.fr/mux.json');
$response = substr($response, 5, -1);
$response = json_decode($response);

if (!$response)
{
  // var_dump('feed not ready');
  die();
}

foreach ($response as $post)
{
  if ($post->type == 'cil.comment')
  {
    if($db['last_update'] == $data->itemID)
      continue;

    $db['last_update'] = $data->itemID;
    $data = $post->data;
    //Event
    $event = ':loudspeaker:';
    // extra space for emoji
    $event .= $event ? ' ' : '';

    postToSlack($event.''.$data->comment.' - '.$data->time, $data->author_avatar, $data->author_name);
  }
}

file_put_contents($dbFile, json_encode($db));