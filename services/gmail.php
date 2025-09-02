<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db.php';

function get_google_client() {
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->addScope(Google_Service_Gmail::GMAIL_SEND);
    $client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    return $client;
}

function store_token($token) {
    file_put_contents(TOKEN_FILE, json_encode($token));
}

function load_token() {
    if (!file_exists(TOKEN_FILE)) return null;
    $t = json_decode(file_get_contents(TOKEN_FILE), true);
    return $t;
}

function gmail_send_email($to, $subject, $body) {
    $client = get_google_client();
    $token = load_token();
    if (!$token) {
        add_log('gmail','error','no token; user must authenticate');
        return false;
    }
    $client->setAccessToken($token);
    // refresh if expired
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            store_token($client->getAccessToken());
        } else {
            add_log('gmail','error','access token expired and no refresh token');
            return false;
        }
    }

    $service = new Google_Service_Gmail($client);

    $strRawMessage = "From: me\r\n";
    $strRawMessage .= "To: {$to}\r\n";
    $strRawMessage .= "Subject: {$subject}\r\n";
    $strRawMessage .= "MIME-Version: 1.0\r\n";
    $strRawMessage .= "Content-Type: text/plain; charset=utf-8\r\n\r\n";
    $strRawMessage .= $body;

    $raw = base64_encode($strRawMessage);
    $raw = str_replace(['+', '/', '='], ['-', '_', ''], $raw);

    $msg = new Google_Service_Gmail_Message();
    $msg->setRaw($raw);

    try {
        $service->users_messages->send("me", $msg);
        add_log('gmail','info',"Email sent to {$to}");
        return true;
    } catch (Exception $e) {
        add_log('gmail','error','send failed: ' . $e->getMessage());
        return false;
    }
}
