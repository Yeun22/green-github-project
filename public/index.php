<?php
$statsPath = __DIR__ . '/stats.json';
$stats = json_decode(@file_get_contents($statsPath), true) ?: [
  'last_commit_iso' => null,
  'last_commit_msg' => null,
  'streak_days' => 0,
  'total_commits' => 0
];
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$last = $stats['last_commit_iso'] ? (new DateTime($stats['last_commit_iso']))->setTimezone(new DateTimeZone('Europe/Paris')) : null;
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Green Graph – Stats</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
    body { margin: 0; background: #0b1220; color: #e7ecf3; }
    .wrap { max-width: 880px; margin: 0 auto; padding: 32px; }
    .card { background: #0f172a; border: 1px solid #1e293b; border-radius: 16px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,.25);}
    h1 { margin: 0 0 16px; font-size: 28px; }
    .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-top: 16px; }
    .metric { background: #111827; border: 1px solid #1f2937; border-radius: 14px; padding: 16px; }
    .metric h2 { margin: 0; font-size: 14px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .06em;}
    .metric .v { margin-top: 8px; font-size: 32px; font-weight: 700; }
    .muted { color: #94a3b8; }
    .footer { margin-top: 20px; font-size: 13px; color: #93a4c2; }
    a { color: #7dd3fc; text-decoration: none; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Green Graph – Activity</h1>
      <p class="muted">Dernier commit : <strong><?php echo $last ? h($last->format('Y-m-d H:i:s T')) : '—'; ?></strong>
        <?php if ($stats['last_commit_msg']): ?> — <em><?php echo h($stats['last_commit_msg']); ?></em><?php endif; ?>
      </p>
      <div class="grid">
        <div class="metric"><h2>Streak (jours d'affilée)</h2><div class="v"><?php echo (int)$stats['streak_days']; ?></div></div>
        <div class="metric"><h2>Total commits</h2><div class="v"><?php echo (int)$stats['total_commits']; ?></div></div>
      </div>
      <div class="footer">Ce dashboard lit <code>public/stats.json</code> mis à jour par un cron.</div>
    </div>
  </div>
</body>
</html>
