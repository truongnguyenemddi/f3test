<?php
  $user = $SESSION['user'] ?? null;
?>
<header class="admin-header">
  <div class="header-container">
    <div class="header-left">
      <h1 class="site-title"><?php echo $SITE_NAME; ?></h1>
    </div>
    <div class="header-right">
      <?php if ($user): ?>
        <span class="user-info">
          Welcome, <strong><?php echo htmlspecialchars($user['full_name'] ?? $user['user_name'] ?? 'User'); ?></strong>
        </span>
        <a href="/logout" class="btn-logout">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</header>
