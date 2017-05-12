<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?= $data['title'] ?></h3>
	</div>
	<div id="<?= $data['name'] ?>Table"></div>
</div>
<script>
$(function () {
	'use strict';

	var tableContainer = $('#<?= $data['name'] ?>Table').html("");
	var dataTable = $('<table class="table table-striped table-hover"></table>').appendTo(tableContainer).DataTable({
		dom: '<"row"<"pull-left"l><"pull-right"f>>t<"panel-footer"<"row"<"pull-left"<"#dt-buttons">><"pull-right"p>>>',
		columns: $.merge(
			$.parseJSON('<?= json_encode($data['tableColumns']) ?>'),
			[{
				title: "", name: 'actions', searchable: false, orderable: false,
				defaultContent:
					'<button type="button" class="btn btn-info" data-action="<?= $data['editAction']['name'] ?>"><i class="fa fa-pencil-square-o"></i> Edit</button> ' +
					'<button type="button" class="btn btn-danger" data-action="<?= $data['removeAction']['name'] ?>"><i class="fa fa-times"></i> Remove</button>',
				createdCell: function (td, cellData, rowData, row, col) {
					$('[data-action="<?= $data['editAction']['name'] ?>"]', td).click(function () {
						WMS.ajax.getPage('<?= $data['url'] ?>', function (form) {
							var $form = $(form);
							var $formSubmit = $form.find(':submit');
							$formSubmit.closest('.form-group').hide();
							var dialog = bootbox.dialog({
								title: $.replaceVars("<?= $data['editAction']['title'] ?>", rowData),
								className: '<?= $data['editAction']['name'] ?>Dialog',
								message: $form,
								size: 'large',
								onShown: function () {
									$('#<?= $data['editAction']['name'] ?>Form', dialog).ajaxForm({
										loadingText: "Saving...",
										doneText: "Saved",
										inputs: $('button', dialog),
										submit: $('.<?= $data['editAction']['name'] ?>-submit', dialog),
										success: function () {
											dialog.modal('hide');
											dataTable.ajax.reload($.noop, false);
										}
									});
								},
								buttons: {
									cancel: {
										label: "Cancel",
										className: "btn-default"
									},
									submit: {
										label: $formSubmit.text(),
										className: "btn-primary <?= $data['editAction']['name'] ?>-submit",
										callback: function () {
											$('#<?= $data['editAction']['name'] ?>Form', dialog).submit();
											return false;
										}
									}
								}
							});
						}, $.extend({ view: '<?= (isset($data['editAction']['formViewer']) ? $data['editAction']['formViewer'] : 'editForm' ) ?>' }, $.valueVars($.parseJSON('<?= json_encode($data['editAction']['data']) ?>'), rowData)))
					});
					$('[data-action="<?= $data['removeAction']['name'] ?>"]', td).click(function () {
						bootbox.dialog({
							title: $.replaceVars("<?= $data['removeAction']['title'] ?>", rowData),
							message: $.replaceVars("<?= $data['removeAction']['message'] ?>", rowData),
							buttons: {
								cancel: {
									label: "Cancel",
									className: "btn-default"
								},
								remove: {
									label: "Remove",
									className: "btn-danger",
									callback: function () {
										WMS.ajax.action('<?= $data['url'] ?>', '<?= $data['removeAction']['name'] ?>', {
											success: function () {
												dataTable.ajax.reload($.noop, false);
											},
											data: $.valueVars($.parseJSON('<?= json_encode($data['removeAction']['data']) ?>'), rowData)
										});
									}
								}
							},
							onEscape: true
						});
					});
				}
			}]),
		ajax: {
			type: "POST",
			dataType: "json",
			url: "<?= $data['url'] ?>",
			data: $.extend({
				action: '<?= $data['getAction']['name'] ?>'
			}, <?= ((isset($data['getAction']['data'])) ? "$.parseJSON('" . json_encode($data['getAction']['data']) . "')" : "{}" ) ?>)
		},
		initComplete: function () {
			$('<a class="btn btn-primary"><i class="fa fa-plus"></i> <?= $data['addAction']['title'] ?></a>').appendTo($('#<?= $data['name'] ?> #dt-buttons')).click(function () {
				WMS.ajax.getPage('<?= $data['url'] ?>', function (form) {
					var $form = $(form);
					var $formSubmit = $form.find(':submit');
					$formSubmit.closest('.form-group').hide();
					var dialog = bootbox.dialog({
						title: "<?= $data['addAction']['title'] ?>",
						className: '<?= $data['addAction']['name'] ?>Dialog',
						message: $form,
						size: 'large',
						onShown: function () {
							$('#<?= $data['addAction']['name'] ?>Form', dialog).ajaxForm({
								loadingText: "Adding...",
								doneText: "Added",
								inputs: $('button', dialog),
								submit: $('.<?= $data['addAction']['name'] ?>-submit', dialog),
								success: function () {
									dialog.modal('hide');
									dataTable.ajax.reload($.noop, false);
								}
							});
						},
						buttons: {
							cancel: {
								label: "Cancel",
								className: "btn-default"
							},
							submit: {
								label: $formSubmit.text(),
								className: "btn-primary <?= $data['addAction']['name'] ?>-submit",
								callback: function () {
									$('#<?= $data['addAction']['name'] ?>Form', dialog).submit();
									return false;
								}
							}
						}
					});
				}, $.extend({ view: '<?= (isset($data['addAction']['formViewer']) ? $data['addAction']['formViewer'] : 'addForm' ) ?>' }, <?= ((isset($data['addAction']['data'])) ? "$.parseJSON('" . json_encode($data['addAction']['data']) . "')" : "{}" ) ?>));
			});
		}
	});
});
</script>
