<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
require_once 'db.php';

session_start();

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope(Google_Service_Gmail::GMAIL_SEND);
$client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);
$client->setAccessType('offline');
$client->setPrompt('consent');

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
} else {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    // store token to TOKEN_FILE
    file_put_contents(TOKEN_FILE, json_encode($token));
    echo "<h2>Success ✅</h2><p>Token saved. Close this window and return to the app.</p>";
}
