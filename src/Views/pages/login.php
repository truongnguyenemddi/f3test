<div class="content">
	<h2>Login</h2>

	<?php $app = \Base::instance(); ?>

	<?php if ($flash = $app->get('SESSION.flash_error')): ?>
		<div style="padding:10px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;margin:10px 0;">
			<?= htmlspecialchars($flash) ?>
		</div>
		<?php $app->clear('SESSION.flash_error'); ?>
	<?php endif; ?>

	<?php if ($flash = $app->get('SESSION.flash_success')): ?>
		<div style="padding:10px;border:1px solid #badbcc;background:#d1e7dd;color:#0f5132;margin:10px 0;">
			<?= htmlspecialchars($flash) ?>
		</div>
		<?php $app->clear('SESSION.flash_success'); ?>
	<?php endif; ?>

	<form method="POST" action="/login" style="max-width:360px;margin-top:12px;">
		<div style="margin-bottom:10px;">
			<label style="display:block;margin-bottom:6px;">Username / Email</label>
			<input type="text" name="identity" autocomplete="username" style="width:100%;padding:8px;" required>
		</div>
		<div style="margin-bottom:12px;">
			<label style="display:block;margin-bottom:6px;">Password</label>
			<input type="password" name="password" autocomplete="current-password" style="width:100%;padding:8px;" required>
		</div>
		<button type="submit" style="padding:8px 12px;">Login</button>
	</form>
</div>
