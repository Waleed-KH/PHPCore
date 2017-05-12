<!DOCTYPE html>
<!--[if lt IE 7]>		<html lang="en" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>			<html lang="en" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>			<html lang="en" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>WMS</title>
	<link rel="stylesheet" href="/css/font-awesome.min.css">
	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<!--<link rel="stylesheet" href="/css/bootstrap-theme.min.css">-->
	<link rel="stylesheet" href="/css/jquery-ui.min.css">
	<link rel="stylesheet" href="/css/metisMenu.min.css">
	<!--<link rel="stylesheet" href="/css/switchery.min.css">-->
	<link rel="stylesheet" href="/css/select2.min.css">
	<link rel="stylesheet" href="/css/select2-bootstrap.min.css">
	<!--<link rel="stylesheet" href="/css/jquery.fileupload.min.css">-->
	<link rel="stylesheet" href="/css/notify.min.css">
	<link rel="stylesheet" href="/css/datatables.min.css">
	<link rel="stylesheet" href="/css/bootstrap-switch.min.css">
	<link rel="stylesheet" href="/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="/css/bootstrap-datepicker3.min.css">
	<link rel="stylesheet" href="/css/main.min.css">
	<script src="/js/modernizr-2.8.3.min.js"></script>
	<script src="/js/jquery-2.2.4.min.js"></script>
	<script src="/js/jquery-ui.min.js"></script>
	<script src="/js/bootstrap.js"></script>
	<!--<script src="/js/jquery.canvasjs.min.js"></script>
	<script src="/js/Chart.bundle.js"></script>-->
</head>
<body id="body" class="pt50">
	<div id="wrapper">
		<nav id="navigation-top" class="navbar navbar-default navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<div id="navbar-top-toggle" style="display: none;">
						<button type="button" class="navbar-toggle collapsed" id="navbar-toggle-collapsed" data-toggle="collapse" data-target="#navbar-top-collapse" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<a class="navbar-brand" id="logo" href="javascript:void(0)">WMS</a>
				</div>
				<div id="navbar-top" style="display: none;">
					<div class="collapse navbar-collapse" id="navbar-top-collapse">
						<ul class="nav navbar-nav" id="navbar-top-left">
						</ul>
						<ul class="nav navbar-nav navbar-right" id="navbar-top-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span id="navbar-user-name"></span><span class="caret"></span></a>
								<ul class="dropdown-menu" style="-moz-animation-duration: 0.5s; -o-animation-duration: 0.5s; -webkit-animation-duration: 0.5s; animation-duration: 0.5s;">
									<li><a href="#" disabled><i class="fa fa-user fa-fw"></i> My Account</a></li>
									<li><a href="#" disabled><i class="fa fa-cog fa-fw"></i> Account Settings</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="#" data-action="signout"><i class="fa fa-sign-out fa-fw"></i> Sign out</a></li>
								</ul>
							</li>
						</ul>

					</div>
				</div>
			</div>
		</nav>
		<div id="contentWrapper" style="display: none;">
		</div>
		<div class="spinner" id="loading-spinner">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
	</div>
	<script src="/js/jquery.form.min.js"></script>
	<script src="/js/validator.min.js"></script>
	<script src="/js/bootbox.js"></script>
	<script src="/js/metisMenu.min.js"></script>
	<!--<script src="/js/switchery.min.js"></script>-->
	<script src="/js/uikit-core.min.js"></script>
	<script src="/js/notify.min.js"></script>
	<script src="/js/bootstrap3-typeahead.min.js"></script>
	<script src="/js/select2.full.min.js"></script>
	<!--<script src="/js/jquery.fileupload.min.js"></script>
	<script src="/js/jquery.fileDownload.min.js"></script>-->
	<script src="/js/datatables.js"></script>
	<script src="/js/dataTables.rowsGroup.min.js"></script>
	<script src="/js/bootstrap-switch.min.js"></script>
	<script src="/js/bootstrap-select.min.js"></script>
	<script src="/js/bootstrap-datepicker.min.js"></script>
	<!--<script src="/js/Chart.min.js"></script>-->
	<script src="/js/wms.js"></script>
	<script>
		$(function () {
			$.fn.select2.defaults.set("theme", "bootstrap");
		});
	</script>
</body>
</html>
