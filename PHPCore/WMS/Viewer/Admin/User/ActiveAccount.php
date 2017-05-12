<section id="activeSection">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<h1>Active User</h1>
				</div>
				<form class="form-horizontal pt20" action="/User/ActiveAccount" id="activeUserForm" data-toggle="validator" role="form">
					<input type="hidden" name="action" value="activeUser">
					<div id="checkActive">
						<div class="form-group">
							<label for="id" class="col-sm-2 control-label">ID</label>
							<div class="col-sm-10">
								<input type="text" name="id" class="form-control" id="id" placeholder="ID" autocomplete="off" required autofocus>
							</div>
						</div>
						<div class="form-group">
							<label for="activeCode" class="col-sm-2 control-label">Active Code</label>
							<div class="col-sm-10">
								<input type="password" name="activeCode" class="form-control" id="activeCode" placeholder="Active Code" autocomplete="off" required>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10 btn-container">
								<button type="button" class="btn btn-default" data-action="activeCheck">Check Activation</button>
							</div>
						</div>
					</div>
					<div id="finishActive" style="display: none;">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">User Info</h3>
							</div>
							<div class="panel-body">

								<div class="form-group">
									<label for="firstName" class="col-sm-2 control-label">First name</label>
									<div class="col-sm-10">
										<input type="text" name="user[firstName]" class="form-control" id="firstName" placeholder="First name" autocomplete="off" maxlength="50" data-minLength="2" required pattern="^[a-zA-Z][a-zA-Z ,.\'-]+$">
									</div>
								</div>
								<div class="form-group">
									<label for="lastName" class="col-sm-2 control-label">Last name</label>
									<div class="col-sm-10">
										<input type="text" name="user[lastName]" class="form-control" id="lastName" placeholder="Last name" autocomplete="off" maxlength="50" data-minLength="2" required pattern="^[a-zA-Z][a-zA-Z ,.\'-]+$">
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="username" class="col-sm-2 control-label">Username</label>
									<div class="col-sm-10">
										<input type="text" name="user[username]" class="form-control" id="username" placeholder="Username" autocomplete="off" pattern="^[a-zA-Z][a-zA-Z0-9]*[._-]?[a-zA-Z0-9]+$" maxlength="20" data-minlength="5" data-remote="/User/ActiveAccount?action=userValUsername" required data-pattern-error="This username is invalid (only alphabetic characters & numbers & _)" data-minlength-error="Minimum of 5 characters" data-maxlength-error="Maximum of 20 characters" data-remote-error="This username is already used">
										<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="email" class="col-sm-2 control-label">Email</label>
									<div class="col-sm-10">
										<input type="email" name="user[email]" class="form-control" id="email" placeholder="Email" autocomplete="off" required data-remote="/User/ActiveAccount?action=userValEmail" data-pattern-error="This email is invalid (only alphabetic characters & numbers & _)" data-remote-error="This Email is already used">
										<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="password" class="col-sm-2 control-label">Password</label>
									<div class="col-sm-10">
										<input type="password" name="user[password]" class="form-control" id="password" placeholder="Password" autocomplete="off" data-minlength="6" required data-minlength-error="Minimum of 6 characters">
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="passwordConfirm" class="col-sm-2 control-label">Password Confirm</label>
									<div class="col-sm-10">
										<input type="password" name="user[passwordConfirm]" class="form-control" id="passwordConfirm" placeholder="Confirm" data-match="#password" autocomplete="off" data-minlength="6" required data-match-error="Whoops, these don't match">
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Student Info</h3>
							</div>
							<div class="panel-body">
								<div class="form-group">
									<label for="englishName" class="col-sm-2 control-label">English name</label>
									<div class="col-sm-10">
										<input type="text" name="student[englishName]" class="form-control" id="englishName" placeholder="English name" autocomplete="off" maxlength="50" data-minLength="2" required pattern="^[a-zA-Z][a-zA-Z ,.\'-]+$">
									</div>
								</div>
								<div class="form-group">
									<label for="gender" class="col-sm-2 control-label">Gender</label>
									<label class="radio-inline col-sm-2 control-label">
										<input type="radio" name="student[gender]" id="genderMale" value="1" required>
										Male
									</label>
									<label class="radio-inline col-sm-2 control-label">
										<input type="radio" name="student[gender]" id="genderFemale" value="2" required>
										Female
									</label>
								</div>
								<div class="form-group has-feedback">
									<label for="birthDate" class="col-sm-2 control-label">Birth Date</label>
									<div class="col-sm-10">
										<input type="date" name="student[birthDate]" class="form-control" id="birthDate" autocomplete="off" required>
									</div>
								</div>
								<div class="form-group has-feedback">
									<label for="address" class="col-sm-2 control-label">Address</label>
									<div class="col-sm-10">
										<input type="text" name="student[address]" class="form-control" id="address" placeholder="Address" autocomplete="off" required >
									</div>
								</div>
								<div class="form-group">
									<label for="tel" class="col-sm-2 control-label">Phone Number</label>
									<div class="col-sm-10">
										<input type="tel" name="student[phoneNo]" class="form-control" id="tel" placeholder="Tel" autocomplete="off" data-minlength="11" maxlength="11" required data-minlength-error="Minimum of 11 Number">
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 btn-container text-right">
								<button type="submit" class="btn btn-default">Active My Account</button>
							</div>
						</div>
					</div>

				</form>
				<p class="text-left" style="margin-left:20px">
					<a href="" data-ajax-container="contentWrapper">&lt;&lt; User Login</a>
				</p>
			</div>

		</div>
	</div>
</section>
<script>
	$('#finishActive :input').prop("disabled", true);

	var activeUserForm = $('#activeUserForm');
	activeUserForm.validator().ajaxForm({
		loadingText: "Activating...",
		doneText: "Activated",
		success: function (msg) {
			if (msg.result) {
				window.setTimeout(function () {
					activeUserForm.hide(function () { FIS.User.CheckLogin(null, true); });
				}, 2500);
			}
		}
	});

	var activeCheckButton = $('#checkActive button[data-action=activeCheck]');

	activeCheckButton.click(function (e) {
		var thisText = activeCheckButton.html();
		activeCheckButton.html('Checking...');
		$(':input', activeUserForm).prop("disabled", true);

		var err = function () {
			activeCheckButton.html('Error!');
			window.setTimeout(function () {
				activeCheckButton.html(thisText);
				$('#checkActive :input').prop("disabled", false);
			}, 5000);
		};

		FIS.ajax.action('/User/ActiveAccount', 'activeCheck', {
			data: {
				id: $('#id').val(),
				activeCode: $('#activeCode').val()
			},
			success: function (msg) {
				if (msg.result) {
					activeCheckButton.html('Checked');
					//$('#firstName').val(msg.fname);
					//$('#lastName').val(msg.lname);
					$('#checkActive').fadeOut(500, function () {
						$(':input', activeUserForm).prop("disabled", false);
						var $username = $('#username');
						var $email = $('#email');
						$username.attr('data-remote', '/User/ActiveAccount?action=userValUsername&userId=' + $('#id').val());
						$email.attr('data-remote', '/User/ActiveAccount?action=userValEmail&userId=' + $('#id').val());
						$('#finishActive').fadeIn(500);
					});
				} else {
					err();
				}
			},
			error: err
		});

	});

	$('#checkActive input').keypress(function (e) {
		if (e.keyCode == 13)
			activeCheckButton.click();
	});
</script>
