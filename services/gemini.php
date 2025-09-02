<?php
require_once __DIR__ . '/../config.php';

function gemini_generate($prompt) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";
    $data = [
      "contents" => [
         ["parts" => [["text" => $prompt]]]
      ]
    ];
    $ch = curl_init($url . "?key=" . GEMINI_API_KEY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $res = curl_exec($ch);
    if ($res === false) {
        add_log('gemini','error',curl_error($ch));
        curl_close($ch);
        return null;
    }
    curl_close($ch);
    $j = json_decode($res, true);
    // extract simple text
    $parts = $j['candidates'][0]['content'][0]['parts'][0]['text'] ?? null;
    return $parts;
}
