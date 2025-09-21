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
<html lang="fr" data-theme="dark">
<head>
  <meta charset="utf-8">
  <title>Green Graph – Stats</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Tableau de bord des statistiques Git avec suivi des commits et streak">
  <link rel="stylesheet" href="./style.css">
</head>
<body>
  <!-- Theme Toggle Button -->
  <button class="theme-toggle" onclick="toggleTheme()" aria-label="Basculer entre thème clair et sombre">
    <svg id="theme-icon" viewBox="0 0 24 24">
      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
  </button>

  <div class="container">
    <!-- Header Section -->
    <div class="header">
      <h1>Green Graph</h1>
      <p>Suivi de votre activité Git en temps réel</p>
    </div>

    <!-- Main Dashboard Card -->
    <div class="main-card">
      <!-- Last Commit Information -->
      <div class="last-commit">
        <div class="label">Dernier commit</div>
        <div class="value">
          <?php echo $last ? h($last->format('d/m/Y à H:i')) : 'Aucun commit'; ?>
        </div>
        <?php if ($stats['last_commit_msg']): ?>
          <div class="message"><?php echo h($stats['last_commit_msg']); ?></div>
        <?php endif; ?>
      </div>

      <!-- Metrics Grid -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="label">Streak actuel</div>
          <div class="value"><?php echo (int)$stats['streak_days']; ?></div>
          <div class="description">jours consécutifs</div>
        </div>
        
        <div class="metric-card">
          <div class="label">Total commits</div>
          <div class="value"><?php echo (int)$stats['total_commits']; ?></div>
          <div class="description">contributions totales</div>
        </div>
      </div>

      <!-- Footer -->
      <div class="footer">
        Ce tableau de bord lit automatiquement <code>stats.json</code> mis à jour par un script cron.
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
