// Configure Chart.js globals
Chart.defaults.global.responsive = true;
Chart.defaults.global.maintainAspectRatio = false;
Chart.defaults.Pie.animationEasing = 'easeOutQuart';
Chart.defaults.Doughnut.animationEasing = 'easeOutQuart';

jQuery(document).ready(function($) {

	// Viewport resize event
	$(window).resize(function() {
		$('.nodes-center').each(function() {
			Nodes.centerInViewport($(this));
		});
	}).resize();

	// Add "roll effect" to links
	$('a.nodes-link').each(function() {
		Nodes.linkEffect($(this));
	});

	// Popover
	$('[data-popover="true"]').each(function() {
		var trigger = $(this).data('trigger');
		$(this).popover({
			trigger: trigger ? trigger : 'click',
			html: true
		});
	});

	// Tooltips
	$('[data-toggle="tooltip"]').tooltip();
	// For elements that has another data-toggle attribute defined
	$('[data-tooltip="true"]').tooltip();

	// Highlight selected radio-/checkboxes
	$('.checkbox,.radio').each(function() {
		$(this).find(':radio,:checkbox').click(function() {
			if ($(this).is(':checked')) {
				if ($(this).attr('type') == 'radio') {
					$(this).parents('.radio').find('label').addClass('selected')
				} else {
					$(this).parents('.checkbox').find('label').addClass('selected');
				}
			} else {
				if ($(this).attr('type') == 'radio') {
					$(this).parents('.radio').find('label').removeClass('selected')
				} else {
					$(this).parents('.checkbox').find('label').removeClass('selected');
				}
			}
		})
	});

	// Select all checkbox/radio buttons
	$('.nodes-select-all[data-target]').each(function() {
		Nodes.selectAll($(this));
	});

	// Confirm dialog
	$('[data-confirm="true"]').each(function() {
		Nodes.confirmModal($(this));
	});

	// Confirm deletion dialog
	$('[data-delete="true"]').each(function() {
		Nodes.confirmDelete($(this));
	});

	// Slugify element
	$('input[type="text"][data-slugify],textarea[data-slugify]').on('input', function() {
		Nodes.slugifyElement($(this));
	});

	// WYSIWYG
	$('[data-wysiwyg="true"]').each(function() {
		Nodes.wysiwyg($(this));
	});

	//----------------------------------------------------------------
	// Roles
	//----------------------------------------------------------------
	$('#roleModal').each(function() {
		// Role modal
		var modal = $(this).modal({
			'show': false
		});

		// Default form values
		var modalDefaults = {
			'title': modal.find('.modal-title').text(),
			'action': modal.find('form').attr('action'),
			'button': modal.find('form [type="submit"]').text()
		};

		// Attach modal events
		modal.on('show.bs.modal', function(e) {
			// Event trigger
			var trigger = $(e.relatedTarget);

			// Only continue if the button clicked
			// is our 'edit role' button
			if (!trigger.hasClass('role-edit')) {
				return;
			}

			// Pre-fill form with role name
			$(this).find('.modal-title').text('Edit role').end()
				.find('form').attr('action', trigger.data('href'))
				.prepend($('<input/>', {
					'name': '_method',
					'type': 'hidden',
					'class': 'role-edit',
					'value': 'PATCH'
				}), $('<input/>', {
					'name': 'id',
					'type': 'hidden',
					'class': 'role-edit',
					'value': trigger.data('role-id')
				})).end()
				.find('#roleName').val(trigger.data('role')).end()
				.find('[type="submit"]').text('Edit role');
		}).on('hidden.bs.modal', function(e) {
			// Reset form to initial state
			$(this).find('.modal-title').text(modalDefaults.title).end()
				.find('form').attr('action', modalDefaults.action)
				.find('.role-edit').remove().end()
				.find('#roleName').val('').end()
				.find('[type="submit"]').text(modalDefaults.button);
		});
	});

	//----------------------------------------------------------------
	// Capabilities
	//----------------------------------------------------------------
	$('#rolesCapabilitiesCapabilityModal').each(function() {
		var group = $(this).find('#capabilityGroup');
		var capabilityName = $(this).find('#capabilityName');
		var capabilitySlug = $(this).find('#capabilitySlug');
		var capabilitySlugPreview = $(this).find('#capabilitySlugPreview');

		// Create re-usable callback
		var updateCapabilitySlug = function(value) {
			// Slugify capability name
			var capabilityName = Nodes.slugify(value);

			// Prepend selected group slug (if one is selected)
			var groupSlug = group.find('option:selected').data('slug');
			if (groupSlug) {
				capabilityName = groupSlug + '_' + capabilityName;
			}

			// Set generated capability slug
			if (capabilityName) {
				capabilitySlug.val(capabilityName);
				capabilitySlugPreview.text(capabilityName);
			} else {
				capabilitySlug.val('');
				capabilitySlugPreview.text('N/A');
			}
		};

		// Attach events to group and capability name
		capabilityName.on('input', function() {
			updateCapabilitySlug($(this).val())
		});
		group.on('change', function() {
			updateCapabilitySlug(capabilityName.val());
		})
	});

	$('.capabilities-wrapper').each(function() {
		var rolesCapabilities = $(this);
		$(this).find(':checkbox').click(function() {
			if (rolesCapabilities.find('.capabilities-list :checked').length > 0) {
				rolesCapabilities.find('button[type="submit"]').addClass('btn-danger').removeClass('disabled').prop('disabled', false).attr('aria-disabled', 'false');
			} else {
				rolesCapabilities.find('button[type="submit"]').removeClass('btn-danger').addClass('disabled').prop('disabled', true).attr('aria-disabled', 'true');
			}
		});
	});

	$('#capabilities-toggle-slug :checkbox').each(function() {
		Nodes.capabilityToggleSlug($(this));
	});
})