<?php
$app = \Base::instance();
$loginState = (string) $app->get('GET.login');
$errorText = '';
if ($loginState === 'failed')
	$errorText = 'Sai tên đăng nhập hoặc mật khẩu !!!';
if ($loginState === 'denied')
	$errorText = 'Bạn chưa được cấp quyền truy cập hệ thống !!!';
if (!$errorText && ($flash = $app->get('SESSION.flash_error'))) {
	$errorText = (string) $flash;
	$app->clear('SESSION.flash_error');
}
?>

<style>
	#LoginBox {
		width: 520px;
		margin: 0 auto;
	}

	#LoginForm {
		margin-left: 40px;
	}

	body,
	form,
	div {
		font-family: tahoma;
		font-size: 12px;
	}

	.cls-footer {
		font-size: 11px;
		font-style: italic;
		color: #808080;
	}

	.lbl-remember {
		display: inline-block;
		margin-left: 20px;
		vertical-align: middle;
		font-size: 11px;
		font-weight: normal;
	}

	#Remember {
		margin: 0;
		padding: 0;
		position: absolute;
	}

	.notify {
		font-size: 11px;
		font-style: italic;
		font-weight: normal;
	}

	.login-container {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100vh;
		width: 100%;
	}

	@media (max-width: 576px) {
		#LoginBox {
			width: auto;
			margin: 24px 12px 0;
		}

		#LoginForm {
			margin: 0 14px;
		}
	}
</style>
<center class="login-container">
	<div id="LoginBox" class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><b>QUẢN LÝ HỆ THỐNG ĐẶT VÀ ĐIỀU VẬN XE</b></h3>
		</div>
		<div class="panel-body">
			<?php if ($errorText): ?>
				<div class="text-center" style="margin: 8px 0 14px;">
					<span class="label label-danger notify"><?= htmlspecialchars($errorText) ?></span>
				</div>
			<?php endif; ?>

			<div class="row" style="display:flex;align-items:center;justify-content:center;">
				<div class="text-center" style="margin-bottom:12px;">
					<img src="/assets/icons/emddi_logo2.png" alt="Logo" style="width:140px" />
				</div>
				<div class="text-left">
					<form id="LoginForm" method="POST" action="/login" autocomplete="on">
						<div class="form-group" style="width:100%">
							<div class="input-group">
								<div class="input-group-addon"><i class="fa fa-user"></i>&nbsp;</div>
								<input type="text" class="form-control" id="Identity" name="identity"
									placeholder="Tên đăng nhập" autocomplete="username"
									style="width:225px;font-size:12px" required />
							</div>
						</div>
						<div class="form-group" style="width:100%">
							<div class="input-group">
								<div class="input-group-addon"><i class="fa fa-key"></i></div>
								<input type="password" class="form-control" id="Password" name="password"
									placeholder="Mật khẩu" autocomplete="current-password"
									style="width:225px;font-size:12px" required />
							</div>
						</div>
						<div class="form-group" style="width:100%">
							<div class="input-group">
								<input id="Remember" type="checkbox" />
								<label for="Remember" class="lbl-remember">Nhớ tên truy cập</label>
							</div>
						</div>
						<div class="form-group" style="width:100%;margin-top:24px;">
							<div class="input-group">
								<button type="submit" class="btn btn-primary"
									style="font-size:12px;font-weight:bold;width:265px">
									ĐĂNG NHẬP HỆ THỐNG
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="panel-footer cls-footer">Emddi Web Version 2.0.0 © 2026 - Powered by Emddi Co.,Ltd</div>
	</div>
</center>

<script>
	(function () {
		var remember = document.getElementById('Remember');
		var identity = document.getElementById('Identity');
		var password = document.getElementById('Password');

		var storedRemember = localStorage.getItem('remember_identity') === 'true';
		var storedIdentity = localStorage.getItem('identity_value') || '';

		remember.checked = storedRemember;
		if (storedRemember && storedIdentity) {
			identity.value = storedIdentity;
			password.value = '';
			password.focus();
		} else {
			identity.focus();
		}

		document.getElementById('LoginForm').addEventListener('submit', function () {
			localStorage.setItem('remember_identity', remember.checked ? 'true' : 'false');
			if (remember.checked) localStorage.setItem('identity_value', identity.value || '');
			else localStorage.removeItem('identity_value');
		});
	})();
</script>