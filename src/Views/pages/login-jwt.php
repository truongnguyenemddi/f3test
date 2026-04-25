<?php
$app = \Base::instance();
$base = rtrim((string) $app->get('BASE'), '/');
$apiLoginUrl = ($base === '' ? '' : $base) . '/api/v1/login';
?>
<div class="content">
	<h2><?= htmlspecialchars((string) ($page_title ?? 'Login JWT'), ENT_QUOTES, 'UTF-8') ?></h2>

	<div id="jwt-login-alert" style="display:none;padding:10px;margin:10px 0;border:1px solid #ccc;"></div>

	<form id="jwt-login-form" style="max-width:360px;margin-top:12px;">
		<div style="margin-bottom:10px;">
			<label style="display:block;margin-bottom:6px;">Username / Email</label>
			<input type="text" name="identity" autocomplete="username" style="width:100%;padding:8px;" required>
		</div>
		<div style="margin-bottom:12px;">
			<label style="display:block;margin-bottom:6px;">Password</label>
			<input type="password" name="password" autocomplete="current-password" style="width:100%;padding:8px;" required>
		</div>
		<button type="submit" style="padding:8px 12px;">Login via API</button>
	</form>

	<p style="margin-top:16px;font-size:14px;color:#555;">
		After a successful login, the token is stored in <code>localStorage</code> (<code>access_token</code>) to call the API. The web admin page still uses sessions — this is just for testing JWT.
	</p>
</div>

<script>
(function () {
	var form = document.getElementById('jwt-login-form');
	var alertBox = document.getElementById('jwt-login-alert');
	var apiUrl = <?= json_encode($apiLoginUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

	function showAlert(kind, text) {
		alertBox.style.display = 'block';
		alertBox.textContent = text;
		if (kind === 'error') {
			alertBox.style.borderColor = '#f5c2c7';
			alertBox.style.background = '#f8d7da';
			alertBox.style.color = '#842029';
		} else {
			alertBox.style.borderColor = '#badbcc';
			alertBox.style.background = '#d1e7dd';
			alertBox.style.color = '#0f5132';
		}
	}

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		alertBox.style.display = 'none';

		var fd = new FormData(form);

		fetch(apiUrl, {
			method: 'POST',
			body: fd,
			headers: { 'Accept': 'application/json' },
			credentials: 'same-origin'
		})
			.then(function (res) {
				return res.json().then(function (data) {
					return { ok: res.ok, status: res.status, data: data };
				});
			})
			.then(function (result) {
				var d = result.data || {};
				if (result.ok && d.ok && d.access_token) {
					try {
						localStorage.setItem('access_token', d.access_token);
						if (d.expires_in != null) {
							localStorage.setItem('access_token_expires_in', String(d.expires_in));
						}
					} catch (err) {}
					showAlert('success', 'API login successful. Token has been saved to localStorage.');
				} else {
					var err = d.error || ('HTTP ' + result.status);
					showAlert('error', 'Login failed: ' + err);
				}
			})
			.catch(function () {
				showAlert('error', 'Network error or inability to read JSON from the API.');
			});
	});
})();
</script>
