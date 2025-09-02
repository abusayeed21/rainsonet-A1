<?php
require_once __DIR__ . '/../config.php';

function openai_request($data) {
    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $res = curl_exec($ch);
    if ($res === false) {
        add_log('openai','error',curl_error($ch));
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return $res;
}

function ai_chat($message) {
    $data = [
        "model" => "gpt-4o-mini",
        "messages" => [
            ["role" => "system", "content" => "You are a helpful assistant."],
            ["role" => "user", "content" => $message]
        ],
        "max_tokens" => 400
    ];
    $res = openai_request($data);
    if (!$res) return "Error: OpenAI request failed.";
    $j = json_decode($res, true);
    return $j['choices'][0]['message']['content'] ?? "No reply";
}

function ai_detect_intent_openai($prompt) {
    // We instruct the model to output only JSON
    $data = [
        "model" => "gpt-4o-mini",
        "messages" => [
            ["role" => "system", "content" => "You must output only a valid JSON object (no extra text)."],
            ["role" => "user", "content" => $prompt]
        ],
        "max_tokens" => 250
    ];
    $res = openai_request($data);
    if (!$res) return null;
    $j = json_decode($res, true);
    $content = $j['choices'][0]['message']['content'] ?? null;
    // clean (in case model adds backticks)
    $content = trim($content);
    $content = preg_replace('/^`+|`+$/', '', $content);
    return $content;
}
