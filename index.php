<?php
require_once 'config.php';
require_once 'ai_router.php';

$reply = '';
$user_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_message = trim($_POST['message'] ?? '');
    if ($user_message !== '') {
        $reply = handleMessage($user_message);
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>AI Automation — MVP</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* small subtle animation */
    .fade-in { animation: fadeIn 400ms ease forwards; opacity: 0; }
    @keyframes fadeIn { to { opacity: 1; } }
  </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
  <div class="w-full max-w-3xl bg-white shadow-2xl rounded-2xl overflow-hidden">
    <div class="p-6 border-b">
      <h1 class="text-2xl font-semibold">🤖 AI Automation</h1>
      <p class="text-sm text-slate-500 mt-1">Chat normally or request automations — send/schedule email, calendar events, or search web.</p>
    </div>

    <div class="p-6 space-y-4">
      <form method="post" class="flex gap-3">
        <input name="message" value="<?= htmlspecialchars($user_message) ?>"
               class="flex-1 p-3 rounded-lg border shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
               placeholder="Type: 'Send email to test@example.com tomorrow 10am about report'">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Send</button>
      </form>

      <div class="grid grid-cols-2 gap-4">
        <a href="<?= APP_URL . '/callback.php' ?>" class="block text-center p-3 bg-emerald-50 border rounded-lg hover:shadow">
          🔗 Connect Google (Gmail & Calendar)
        </a>
        <div class="p-3 bg-yellow-50 border rounded-lg">
          <strong>Quick Tips:</strong>
          <ul class="text-sm mt-2">
            <li>- "send email to x@y.com subject Hello body ..." </li>
            <li>- "schedule meeting with John tomorrow 5pm" </li>
            <li>- "search latest news about Tesla"</li>
          </ul>
        </div>
      </div>

      <?php if ($user_message !== ''): ?>
      <div class="mt-4">
        <div class="p-4 bg-slate-50 rounded-lg fade-in">
          <p class="text-sm text-slate-600"><strong>You:</strong> <?= nl2br(htmlspecialchars($user_message)) ?></p>
          <hr class="my-3">
          <p class="text-sm text-slate-800"><strong>AI:</strong> <?= nl2br(htmlspecialchars($reply)) ?></p>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
