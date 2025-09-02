<?php
require_once __DIR__ . '/../config.php';

function serpapi_search($query) {
    $q = urlencode($query);
    $url = "https://serpapi.com/search.json?q={$q}&api_key=" . SERPAPI_KEY;
    $res = file_get_contents($url);
    if ($res === false) {
        add_log('serpapi','error','failed to fetch');
        return "Search failed.";
    }
    $j = json_decode($res, true);
    // Simplified: return top 3 organic results titles+links
    $out = [];
    if (!empty($j['organic_results'])) {
        $count = 0;
        foreach ($j['organic_results'] as $r) {
            $out[] = ($r['title'] ?? '') . " — " . ($r['link'] ?? '');
            $count++;
            if ($count >= 3) break;
        }
        return "Top results:\n" . implode("\n", $out);
    }
    return "No results found.";
}
