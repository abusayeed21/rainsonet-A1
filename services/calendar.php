<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db.php';

function calendar_create_event($title, $datetime) {
    $client = get_google_client();
    $token = load_token();
    if (!$token) { add_log('calendar','error','no token'); return false; }
    $client->setAccessToken($token);
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            store_token($client->getAccessToken());
        } else { add_log('calendar','error','token expired'); return false; }
    }

    $service = new Google_Service_Calendar($client);

    // create event (simple single datetime)
    $event = new Google_Service_Calendar_Event([
        'summary' => $title,
        'start' => ['dateTime' => $datetime, 'timeZone' => 'Asia/Kolkata'],
        'end' => ['dateTime' => date('c', strtotime($datetime) + 3600), 'timeZone' => 'Asia/Kolkata'],
    ]);

    try {
        $event = $service->events->insert('primary', $event);
        add_log('calendar','info','event created: ' . $event->getId());
        return true;
    } catch (Exception $e) {
        add_log('calendar','error','create failed: ' . $e->getMessage());
        return false;
    }
}
