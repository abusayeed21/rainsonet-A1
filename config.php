<?php
// config.php — single file to paste your keys (do NOT commit to git)

define('OPENAI_API_KEY', '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'); // ex: sk-...
define('GEMINI_API_KEY', 'AIzaSyCfaTR9Hy8LXfwE1IiNzmI9JbTvkpx-Axk'); // ex: AIza...
define('SERPAPI_KEY', '4b76eb7c5c004f6f860e02ebc04a50b10e8571b3fd91e50157e4363c52f00999'); // ex: 4b76...

// Google OAuth2 (Web app credentials from Google Cloud Console)
define('GOOGLE_CLIENT_ID', '784199393915-pa51dhbcnn8jb6bti1hnll3gs8gt5hr6.apps.googleusercontent.com'); // ex: 7841...
define('GOOGLE_CLIENT_SECRET', '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'); // ex: GOCSPX-...
// Callback URL you set in Google console
define('GOOGLE_REDIRECT_URI', 'http://localhost/callback.php');

// Database (MySQL)
define('DB_HOST', 'localhost');
define('DB_NAME', 'emapescl_rainsonet');      // replace if needed
define('DB_USER', 'emapescl_rainsonet');      // replace if needed
define('DB_PASS', 'QEegTux3K9ha4ZfEEULr');    // replace if needed

// App settings
define('APP_URL', 'http://localhost'); // change if deployed
define('TOKEN_FILE', __DIR__ . '/token.json'); // Google oauth token storage
