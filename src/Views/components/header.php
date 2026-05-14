<header class="admin-header">
  <div class="header-container">
    <div class="header-left">
      <h1 class="site-title"><?php echo $SITE_NAME; ?></h1>
    </div>
    <div class="header-right">
        <span class="user-info">
          Welcome, <strong><?php echo htmlspecialchars($SESSION['fullName'] ?? $SESSION['userName'] ?? 'User'); ?></strong>
        </span>
        <a href="/logout" class="btn-logout">Logout</a>
    </div>
  </div>
</header>
