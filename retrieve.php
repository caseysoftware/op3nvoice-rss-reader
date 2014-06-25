<?php
require 'creds.php';
require 'vendor/autoload.php';

$rssFeed = 'http://atxwebshow.com/feed/podcast/';
$rssString = file_get_contents($rssFeed);

$xml = simplexml_load_string($rssString);

$i = 0;
$itemArray = $xml->channel->item;
foreach ($itemArray as $item) {
    $name = $item->title;
    $description = html_entity_decode($item->description);

    $media = $item->enclosure;
    $attributes = $media->attributes();
    $mediaUrl = $attributes['url'][0];

    if (strlen($mediaUrl)) {
        $jsonData = '{"description": '.json_encode($description).'}';

        $op3nvoice = new OP3Nvoice\Bundle($apikey);
        $result = $op3nvoice->create(array('name' => $name, 'media_url' => $mediaUrl, 'metadata' => $jsonData));

        if ($result['code'] == 201) {
            echo "loaded: " . $mediaUrl . "\n";
        }
    }
    $i++;
}