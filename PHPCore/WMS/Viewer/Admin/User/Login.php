<section id="loginSection">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<form data-toggle="validator" role="form" class="form-horizontal" action="/WMS-Admin/User/Login" method="post" id="loginForm">
					<input type="hidden" name="action" value="login">
					<div class="form-group">
						<label for="username" class="col-sm-2 control-label">Username</label>
						<div class="col-sm-10">
							<input type="text" name="username" class="form-control" id="username" placeholder="Username" autocomplete="off" autofocus required data-minlength="4">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-2 control-label">Password</label>
						<div class="col-sm-10">
							<input type="password" name="password" class="form-control" id="password" placeholder="Password" required data-minlength="6">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-primary">Sign in</button>
						</div>
					</div>
					<!--<p class="text-right">
						<a href="User/ActiveAccount" data-ajax-container="contentWrapper">Active Account &gt;&gt;</a>
					</p>-->
				</form>
			</div>
		</div>
	</div>
</section>
<script>
	(function () {
		$('#loginForm').validator().ajaxForm({
			loadingText: "Signing in...",
			success: function () {
				$('#loginForm').hide(WMS.User.CheckLogin);
			}
		});
	})();
</script>
