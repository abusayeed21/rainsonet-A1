<?php
require_once __DIR__ . '/services/openai.php';
require_once __DIR__ . '/services/gemini.php';
require_once __DIR__ . '/services/serpapi.php';
require_once __DIR__ . '/services/gmail.php';
require_once __DIR__ . '/services/calendar.php';
require_once __DIR__ . '/db.php';

function handleMessage($message) {
    // 1) Ask OpenAI to classify intent and extract entities
    $prompt = "You are an assistant that must decide if the user is chatting or requesting an automation action.\n\n"
            . "Return a JSON object only with keys: action (one of: chat, send_email, schedule_event, search), "
            . "and other params as needed (to, subject, body, datetime, query). Example:\n"
            . "{\"action\":\"send_email\",\"to\":\"a@b.com\",\"subject\":\"Hi\",\"body\":\"Hello\",\"datetime\":null}\n\n"
            . "User message: " . $message;

    $intent_json = ai_detect_intent_openai($prompt);
    if (!$intent_json) {
        add_log('ai_router','error','intent parse failed');
        // fallback: treat as chat
        return ai_chat($message);
    }

    // parse returned JSON
    $intent = json_decode($intent_json, true);
    if (!$intent || !isset($intent['action'])) {
        return ai_chat($message);
    }

    switch ($intent['action']) {
        case 'send_email':
            // if send immediately
            if (!empty($intent['datetime'])) {
                // schedule
                $pdo = get_db();
                $stmt = $pdo->prepare("INSERT INTO scheduled_emails (recipient, subject, body, send_at) VALUES (?, ?, ?, ?)");
                $stmt->execute([$intent['to'], $intent['subject'] ?? '', $intent['body'] ?? '', $intent['datetime']]);
                return "✅ Email scheduled to " . $intent['to'] . " at " . $intent['datetime'];
            } else {
                // send now
                $res = gmail_send_email($intent['to'], $intent['subject'] ?? '', $intent['body'] ?? '');
                return $res ? "📧 Email sent to {$intent['to']}" : "⚠️ Failed to send email (check logs)";
            }
        case 'schedule_event':
            $res = calendar_create_event($intent['title'] ?? 'Event', $intent['datetime'] ?? null);
            return $res ? "📅 Event scheduled." : "⚠️ Failed to schedule event.";
        case 'search':
            $search_res = serpapi_search($intent['query'] ?? $message);
            return $search_res;
        default:
            return ai_chat($message);
    }
}
