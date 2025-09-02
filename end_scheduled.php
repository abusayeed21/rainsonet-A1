<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/services/gmail.php';

$pdo = get_db();
$stmt = $pdo->prepare("SELECT * FROM scheduled_emails WHERE status='pending' AND send_at <= NOW()");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    $ok = gmail_send_email($r['recipient'], $r['subject'], $r['body']);
    if ($ok) {
        $u = $pdo->prepare("UPDATE scheduled_emails SET status='sent' WHERE id=?");
        $u->execute([$r['id']]);
    } else {
        $u = $pdo->prepare("UPDATE scheduled_emails SET status='failed' WHERE id=?");
        $u->execute([$r['id']]);
    }
}
