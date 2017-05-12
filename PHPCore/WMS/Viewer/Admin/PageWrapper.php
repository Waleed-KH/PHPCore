<title><?= $data['title'] ?></title>
<section id="<?= $data['name'] ?>">
	<?php if ($data['fullContainer']) { ?><div id="page-wrapper"><?php } ?>
		<div class="<?= (isset($data['containerClass']) ? $data['containerClass'] : 'container') ?>">
			<div class="row">
				<div class="col-md-12">
					<div class="page-header">
						<h1><?= $data['title'] ?></h1>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php \WMS\Viewer\AdminViewer::PrintView($data['pageViewer'], $data); ?>
				</div>
			</div>
		</div>
	<?php if ($data['fullContainer']) { ?></div><?php } ?>
</section>
