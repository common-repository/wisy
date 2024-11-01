$(document).ready(function () {

$('#wisy-builder-editor-preview>iframe').load(function () {

	var previewIframe = $(this).contents();
	window.previewIframe = previewIframe;

	$('select.st-custom-select', previewIframe).wisySelect();

	// Click inside .wisy-editor-block
	previewIframe.on('click', '.wisy-editor-block', function (e) {
		var closestBlock = ( $(e.target).is('.wisy-editor-block') ) ? $(e.target) : $(e.target).closestParents('.wisy-editor-block');
		wisyActiveBlock(closestBlock);
		return false;
	});

	// Click outside .wisy-editor-block
	previewIframe.on('click', '*', function (e) {
		var closestBlock = ( $(e.target).is('.wisy-editor-block') ) ? $(e.target) : $(e.target).closestParents('.wisy-editor-block');

		if ( closestBlock.length == 0 )
		{
			$('.wisy-editor-block', previewIframe).removeClass('active');
			wisyToggleShowSidebar(closestBlock, 'hide');

			return false;
		}
	});

	previewIframe.on('mouseover focusin', '.wisy-editor-block', function (event) {
		var parentBlocks = $(this).parents('.wisy-editor-block');

		var t = ($(this).hasClass('wisy-editor-row')) ? false : true;

		for (var i = 0; i <= (parentBlocks.length-1); i++) {
			if (t) {
				$(parentBlocks[i]).addClass('is-hover');
			} else {
				$(parentBlocks[i]).removeClass('is-hover');
			}

			if ($(parentBlocks[i]).hasClass('wisy-editor-row')) {
				t = false;
				event.stopPropagation();
			}
		}

		$(this).addClass('is-hover');
	}).on('mouseout focusout', '.wisy-editor-row', function (event) {
		$(event.target).parents('.wisy-editor-block').removeClass('is-hover');
		if ($(event.target).hasClass('wisy-editor-block')) {
			$(event.target).removeClass('is-hover');
		}
	});

	wisyBlocksSortable();

	// Delete block
	previewIframe.on('click', '.wisy-editor-block>.controls span.delete', function () {
		var previewIframe = $('#wisy-builder-editor-preview>iframe').contents(),
			editorArea = $('#wisy-builder-frontend-editor-area', previewIframe),
			thisBlock = $(this).closestParents('.wisy-editor-block');

		wisyDeleteBlock(thisBlock);
	});

	// Duplicate block
	previewIframe.on('click', '.wisy-editor-block>.controls span.duplicate', function () {
		var thisBlock = $(this).closestParents('.wisy-editor-block');
		wisyDuplicateBlock(thisBlock);
	});

	// Add new block
	previewIframe.on('click', '.wisy-add-new-btn button', function () {
		var columnID = $(this).closestParents('.wisy-editor-column').attr('id');
		$('#wisy-builder-new-widget-box', parent.document).attr('data-column-id', columnID);
		$('#wisy-modal-new-block').addClass('open');
	});

	// Add new row
	previewIframe.on('click', 'button.wisy-builder-new-row', function () {
		var editorRowHTML = $('#wisy_builder_editor_row_template', parent.document)[0].innerHTML,
			columnID = $(this).closestParents('.wisy-editor-column').attr('id'),
			newRowID = wisyUniqid('wisy_'),
			newEditorRowID = wisyUniqid('wisy_editor_row_'),
			defaultRowAtts = wisyGetBlockDefAtts('row'),
			editorColumnHTML = $('#wisy_builder_editor_column_template', parent.document)[0].innerHTML;

		editorColumnHTML = editorColumnHTML
			.replace(/\{block_atts\}/g, JSON.stringify({}))
			.replace(/\{block_children\}/g, '')
			.replace(/\{block_id\}/g, wisyUniqid('wisy_'))
			.replace(/\{block_frontend\}/g, '<div class="block-cont column-cont"><div class="before-blocks-container" data-blocks-types="row,widget"></div><div class="wisy-block-children"></div><div class="after-blocks-container" data-blocks-types="row,widget"></div></div>')
			.replace(/\{block_classes\}/g, 'wisy-block wisy-column column-column');

		// New row HTML
		editorRowHTML = editorRowHTML
			.replace(/\{block_atts\}/g, JSON.stringify({}))
			.replace(/\{block_frontend\}/g, '<div class="block-cont row-cont"><div class="before-blocks-container" data-blocks-types="column"></div><div class="wisy-block-children">{block_children}</div><div class="after-blocks-container" data-blocks-types="column"></div></div>')
			.replace(/\{block_children\}/g, editorColumnHTML)
			.replace(/\{block_id\}/g, newRowID)
			.replace(/\{block_classes\}/g, 'wisy-block wisy-row row-row');
		editorRowHTML = wisyTemplaterRender(editorRowHTML, 'data', defaultRowAtts);

		wisyBeforeChangeBlock();

		$('#wisy-builder-frontend-editor-area .wisy-editor-block', previewIframe).removeClass('active');
		var newRowEl = $('#wisy-builder-frontend-editor-area>.wisy-editor-rows', previewIframe).append(editorRowHTML).find('#'+newRowID);
		wisyEditBlockSidebar(newRowEl);
		// Update post content textarea
		wisySavePostBlocks();
	});

	// Add new block (widget, column or row)
	$(document).on('click', '#wisy-builder-new-widget-box .widgets-list li', function () {
		var sidebarCont = $(window.parent.document.getElementById('wisy-builder-main')).find('.wisy-editor-block-settings'),
			columnID = $(this).closestParents('#wisy-builder-new-widget-box').attr('data-column-id'),
			blockID = '',
			blockName = $(this).attr('data-name'),
			previewIframe = $('#wisy-builder-editor-preview>iframe').contents(),
			columnChildrenDiv = $('#'+columnID, previewIframe).find('.wisy-block-children');

			columnChildrenDiv.wisyAddSingleBlock(blockID, blockName, sidebarCont, function (blockHTML, blockID) {
				if (blockName == 'column') {
					$(columnChildrenDiv[0]).closestParents('.wisy-editor-column').after(blockHTML);
				} else {
					$(columnChildrenDiv[0]).append(blockHTML);
				}
			});

		// Close "Add New" box
		$('.wisy-modal header .close').trigger('click');
	});

	$(document).on('keydown', 'body', wisyBuilderShortcuts);

	$(previewIframe).on('keydown', 'body', wisyBuilderShortcuts);

	$.fn.wisyFindNext = function (selector = '') {
		var elements = $(selector, $(this).context.ownerDocument),
			currentIndex = elements.index($(this)),
			nextElements = new Array();

		for (var i = currentIndex+1; i <= elements.length-1; i++) {
			nextElements.push(elements[i]);
		}
		return nextElements;
	};

	$.fn.wisyFindPrev = function (selector = '') {
		var elements = $(selector, $(this).context.ownerDocument),
			currentIndex = elements.index($(this)),
			prevElements = [];

		for (var i = currentIndex-1; i >= 0; i--) {
			prevElements.push(elements[i]);
		}

		return prevElements;
	};

	previewIframe.blockControlsReposition();

	MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

	var observer = new MutationObserver(function(mutations, observer) {
		previewIframe.blockControlsReposition();
	});

	observer.observe(document, {
		subtree: true,
		attributes: true
	});

}); // End preview iframe load

	$.fn.blockControlsReposition = function () {
		$(this).find('.wisy-editor-block>.controls').each(function () {
			$(this).show();
			var rect = $(this)[0].getBoundingClientRect();
			if (rect.left < 0) {
				$(this).css('left', '-1px');
			}
		});
	};

	// Switch between block settings tabs
	$(document).on('click', '#wisy-builder-main .wisy-builder-settings .wisy-block-sections li', function () {
		var settingsCont = $(this).parents('.wisy-editor-block-settings'),
			section = $(this).attr('data-section');
		settingsCont.find('.wisy-block-sections li').removeClass('active');
		$(this).addClass('active');
		settingsCont.find('>.section').removeClass('active');
		settingsCont.find('>.section-' + section).addClass('active');
	});

	// When setting field value changed
	$(document).on('change', '[name^="wisy_setting["]', function () {
		var sidebarCont = $(window.parent.document.getElementById('wisy-builder-main')).find('.wisy-editor-block-settings'),
			blockID = sidebarCont.attr('data-id'),
			previewIframe = $('#wisy-builder-editor-preview>iframe').contents(),
			BlockEl = $('#' + blockID, previewIframe);

		BlockEl.wisyUpdateSingleBlock(
			blockID,
			sidebarCont.attr('data-name'),
			sidebarCont,
			function (blockHTML, blockID) {
				BlockEl.replaceWith(blockHTML);
				BlockEl.css('opacity', '');
			},
			function () {
				BlockEl.css('opacity', '0.3');
			}
		);
	});

	// Close modal box
	$(document).on('click', '.wisy-modal header .close', function () {
		$( $(this).parents('.wisy-modal')[0] ).removeClass('open');
	});

	// When box sides (margin, padding, border-width) values changed
	$(document).on('change', '.wisy-builder-boxsides-fields .group-fields>input', function () {
		var boxSidesCont = $(this).closestParents('.wisy-builder-boxsides-fields'),
			values = boxSidesCont.find('.group-fields>input').allValues(),
			mergedVals = '';
		for (var i in values) {
			mergedVals += (i != 3) ? values[i]+' ' : values[i];
		}
		boxSidesCont.find('>input').val(mergedVals).trigger('change');
	});

	$(document).on('click', '.wisy-builder-responsive', function () {
		$(this).find('>.devices-list').toggleClass('hidden');
		$(this).parent().find('ul').mouseleave(function () {
			var devicesList = $(this);
			$('html').click(function () {
				devicesList.addClass('hidden');
				$('html').unbind('click');
			});
		});
	});

	// On change screen size
	$(document).on('click', '.wisy-builder-responsive .devices-list li', function () {
		var devicesList = $('.wisy-builder-responsive .devices-list'),
			size = $(this).attr('data-size'),
			sizes = {lg: 'monitor', md: 'tablet', sm: 'smartphone'};
		devicesList.find('>li').removeClass('active');
		devicesList.find('>li[data-size="' + size + '"]').addClass('active');
		$('body').removeClass('wisy-on-hover-settings');
		for (var key in sizes) {
			$('body').removeClass('wisy-screen-size-'+key);
		}
		if (size != 'lg') {
			$('body').addClass('wisy-screen-size-'+size);
		}
		$('.wisy-builder-responsive>i').attr('class', 'feather icon-'+sizes[size]);
	});

	// Switch between normal setting and hover setting
	$(document).on('click', '.setting.setting-hover>.setting-args>.single-arg.setting-hover-switcher', function () {
		$('body').removeClass('wisy-screen-size-md').removeClass('wisy-screen-size-sm');
		$('body').toggleClass('wisy-on-hover-settings');
		$('.wisy-builder-responsive>i').attr('class', 'feather icon-monitor');
	});

	// Turn On/Off preview mode
	$(document).on('click', '.button#wisy-builder-preview', function () {
		var previewModeClass = 'wisy-builder-preview-mode',
			previewIframe = $('#wisy-builder-editor-preview>iframe').contents(),
			previewIcon = {
				'on': 'icon-eye',
				'off': 'icon-eye-off'
			};

		if (!$('body').hasClass(previewModeClass)) {
			$('body').first().addClass(previewModeClass);
			$('body', previewIframe).first().addClass(previewModeClass);
			$(this).find('>i').removeClass(previewIcon.on).addClass(previewIcon.off);
		} else {
			$('body').first().removeClass(previewModeClass);
			$('body', previewIframe).first().removeClass(previewModeClass);
			$(this).find('>i').removeClass(previewIcon.off).addClass(previewIcon.on);
		}
	});

	//
	$(document).on('change', '[name^="wisy_post_data["]', function () {
		$('#wisy-builder-save').addClass('unsaved');
	});

	// Save post
	$(document).on('click', '#wisy-builder-header #wisy-builder-save', function () {
		var data = $('[name^="wisy_post_data["]').serializeFullObject(),
			postBlocks = JSON.parse( data['wisy_post_data']['content'] );

		$.ajax({
			url: wisyBuilder.ajaxurl,
			method: 'POST',
			data: {
				'action': 'wisy_save_post',
				'post_id': wisyBuilder.post.id,
				'wisy_post_blocks': postBlocks
			},
			beforeSend: function( xhr )
			{
				$('#wisy-builder-fullpage-loader').addClass('open');
			},
			success: function( response )
			{
				$('#wisy-builder-fullpage-loader').removeClass('open');
				response = JSON.parse( response );
				if ( response.status == 'success' )
				{
					$('#wisy-builder-save').removeClass('unsaved');
				}
			}
		});

	});

	$('#wisy-builder-undo, #wisy-builder-redo').on('click', function () {
		if ( $(this).is('#wisy-builder-undo') )
		{
			wisyHistory('undo');
		}
		else
		{
			wisyHistory('redo');
		}
	});

	/* FUNCTIONS */

	function wisyBuilderShortcuts(e) {
		var activeBlock = wisyActiveBlock();

		if (e.altKey && e.shiftKey && !e.ctrlKey && [38, 40].indexOf(e.keyCode) > -1) { // Shift + Alt + Up arrow or Down arrow
			wisyMoveBlockShortcut(e);
		} else if (e.keyCode == 112 && !e.ctrlKey && !e.altKey) { // F1 Key
			var nextBlocks = $(activeBlock).wisyFindNext('.wisy-editor-block'),
				prevBlocks = $(activeBlock).wisyFindPrev('.wisy-editor-block');
			if (e.shiftKey) {
				activeBlock = ($(prevBlocks).length > 0) ? $(prevBlocks[0]) : activeBlock;
			} else {
				activeBlock = ($(nextBlocks).length > 0) ? $(nextBlocks[0]) : activeBlock;
			}
			wisyActiveBlock(activeBlock);
			$('html, body', previewIframe).animate({
				scrollTop: $(activeBlock).offset().top
			}, 200);
		} else if (e.keyCode == 46) { // Delete Key
			wisyDeleteBlock(activeBlock);
		} else if (e.shiftKey && e.keyCode == 68 && !e.ctrlKey && !e.altKey) { // Shift + D
			wisyDuplicateBlock(activeBlock);
		} else if (e.ctrlKey && [89, 90].indexOf(e.keyCode) > -1) { // Ctrl + Z or Y
			var action = (e.keyCode == 90) ? 'undo' : 'redo';
			wisyHistory( action );
		} else if ( e.keyCode == '27' ) { // Escape Key
			$('.wisy-modal.open').removeClass('open');
		}

	}

	function wisyMoveBlockShortcut(e) {
		var activeBlock = wisyActiveBlock(),
			parentRow = ($(activeBlock).parents('.wisy-editor-row').length > 0) ? $(activeBlock).parents('.wisy-editor-row')[0] : false,
			parentBlock = (activeBlock) ? $(activeBlock).parents('.wisy-editor-block')[0] : false,
			prevBlocks = $(activeBlock).wisyFindPrev('.after-blocks-container, .wisy-editor-block'),
			nextBlocks = $(activeBlock).wisyFindNext('.before-blocks-container, .wisy-editor-block'),
			prevElement = $(activeBlock).prev(),
			nextElement = $(activeBlock).next();

		if (e.key == 'ArrowUp') {
			var blocks = prevBlocks,
				data = {
					'selector': '.after-blocks-container',
					'prev_next': 'prev',
					'prepend_append': 'append',
					'before_after': 'before',
					'first_last': 'first-child',
					'closest_element': prevElement
				};
		} else {
			var blocks = nextBlocks,
				data = {
					'selector': '.before-blocks-container',
					'prev_next': 'next',
					'prepend_append': 'prepend',
					'before_after': 'after',
					'first_last': 'first-child',
					'closest_element': nextElement
				};
		}

		wisyBeforeChangeBlock();

		for (var i = 0; i <= blocks.length-1; i++) {

			if ( $(activeBlock).attr('data-type') == $(blocks[i]).attr('data-type') )
			{
				if ( !$(activeBlock).parent().is( $(blocks[i]).parent() ) && $(activeBlock).parents('.wisy-editor-row').length > 1 ) {
					$( $(activeBlock).parents('.wisy-editor-row')[0] )[data['before_after']]($(activeBlock));
					break;
				}
				$(blocks[i])[data['before_after']]($(activeBlock));
				break;
			}
			else if ( $(activeBlock).attr('data-type') == 'row' && $(data['closest_element']).attr('data-type') == 'widget' )
			{
				$(data['closest_element'])[data['before_after']]($(activeBlock));
				break;
			}
			else if ( $(blocks[i]).is(data['selector']) )
			{
				if ( $(blocks[i]).attr('data-blocks-types').indexOf($(activeBlock).attr('data-type')) >= 0 && $(activeBlock).has($(blocks[i])).length == 0 ) {
					$(blocks[i])[data['prev_next']]()[data['prepend_append']]($(activeBlock));
					break;
				}
			}
			else if ( $(blocks[i]).attr('data-type') == 'row' && e.key == 'ArrowUp' && ['row', 'widget'].indexOf($(activeBlock).attr('data-type')) > -1	
				&& $(activeBlock).parents('.wisy-editor-row').length > 1 )
			{
				$(parentRow)[data['before_after']]($(activeBlock));
				break;
			}

		}
		// Update post content textarea
		wisySavePostBlocks();

	}

	function wisyActiveBlock(block = '') {
		var block = $(block, previewIframe),
			editorArea = $('#wisy-builder-frontend-editor-area', previewIframe),
			activeBlock = editorArea.find('.wisy-editor-block.active');
		activeBlock = (activeBlock.length > 0) ? activeBlock[0] : false;

		if ( $(block).is('.wisy-editor-block') )
		{
			$('.wisy-editor-block', previewIframe).removeClass('active hover');
			$(block, previewIframe).addClass('active');
			wisyEditBlockSidebar($(block));
			activeBlock = $(block);
		}

		return activeBlock;
	}

	function wisyDeleteBlock(block = '', showAlert = true) {
		if ($(block).is('.wisy-editor-block')) {
			if (!showAlert) {
				wisyBeforeChangeBlock();

				$(block).remove();
				// Update post content textarea
				wisySavePostBlocks();
				return;
			}

			wisyConfirm(
				{
					title: 'Confirm',
					msg: 'Are you sure want to delete this block?',
				},
				function (s) {
					if (s) {
						wisyBeforeChangeBlock();

						$(block).remove();
						// Update post content textarea
						wisySavePostBlocks();
					}
				}
			);
		}
	}

	function wisyDuplicateBlock(block = '') {
		if ($(block).is('.wisy-editor-block')) {
			var thisBlock = $(block),
				newBlockHTML = thisBlock[0].outerHTML;

			var blocks = $('.wisy-block[id^="wisy_"]', '<div>'+newBlockHTML+'</div>');

			for ( var block in blocks ) {
				if ( typeof blocks[block].id != 'undefined' ) {
					var idPrefix = blocks[block].id.match(new RegExp(/^wisy_/, 'g')),
						oldBlockID = blocks[block].id.replace(new RegExp(idPrefix[0], 'g'), ''),
						newBlockID = wisyUniqid();
					newBlockHTML = newBlockHTML.replace(new RegExp(oldBlockID, 'g'), newBlockID);
				}
			}

			wisyBeforeChangeBlock();

			thisBlock.before(newBlockHTML).removeClass('active');

			// Update post content textarea
			wisySavePostBlocks();
		}
	}

	function wisyBlocksSortable() {
		$('.wisy-editor-rows', previewIframe).sortable({
			items: '>.wisy-editor-row',
			handle: '>.controls>.move',
			connectWith: '.wisy-editor-rows, .wisy-editor-column>.block-cont>.wisy-block-children',
			placeholder: 'wisy-block-sortable-placeholder wisy-row-sortable-placeholder',
			dropOnEmpty: true,
			zIndex: 999999,
			start: function (event, ui) {
				wisyBeforeChangeBlock();
			},
			change: function (event, ui) {
				$('.wisy-editor-block').removeClass('is-hover');
				ui.placeholder.parents('.wisy-editor-block').addClass('is-hover');
			},
			// Update post content textarea
			update: wisySavePostBlocks
		});
	
		$('.wisy-editor-row>.block-cont>.wrap>.wisy-block-children', previewIframe).sortable({
			items: '>.wisy-editor-column',
			handle: '>.controls>.move',
			connectWith: '.wisy-editor-row>.block-cont>.wrap>.wisy-block-children',
			placeholder: 'wisy-block-sortable-placeholder wisy-column-sortable-placeholder',
			dropOnEmpty: true,
			zIndex: 999999,
			start: function (event, ui) {
				wisyBeforeChangeBlock();
				ui.placeholder.width(ui.item.width());
			},
			change: function(event, ui) {
				$('.wisy-editor-block').removeClass('is-hover');
				ui.placeholder.parents('.wisy-editor-block').addClass('is-hover');
			},
			// Update post content textarea
			update: wisySavePostBlocks
		});

		$('.wisy-editor-column>.block-cont>.wisy-block-children', previewIframe).sortable({
			items: '>.wisy-editor-widget, >.wisy-editor-row',
			handle: '>.controls>.move',
			connectWith: '.wisy-editor-column>.block-cont>.wisy-block-children, .wisy-editor-rows',
			placeholder: 'wisy-block-sortable-placeholder wisy-widget-sortable-placeholder',
			dropOnEmpty: true,
			zIndex: 999999,
			start: function (event, ui) {
				wisyBeforeChangeBlock();
				if (ui.item.hasClass('wisy-editor-row')) {
					ui.placeholder.removeClass('wisy-widget-sortable-placeholder').addClass('wisy-row-sortable-placeholder');
				}
			},
			stop: function (event, ui) {
				if (ui.item.hasClass('wisy-editor-widget') && ui.item.parent().hasClass('wisy-editor-rows')) {
					$(this).sortable('cancel');
					window.wisyHistory.undo.splice(-1,1);
				}
			},
			// Update post content textarea
			update: wisySavePostBlocks
		});
	}

	function wisyConfirm(data, callback) {

		$('#wisy-modal-confirm-box').addClass('open').find('header .modal-title').text(data['title']);
		$('#wisy-modal-confirm-box').find('.modal-body .modal-msg').text(data['msg']);

		$('#wisy-modal-confirm-box button#do-action').on('click', function () {
			if (typeof callback == 'function') {
				callback(true);
			}
			$('#wisy-modal-confirm-box').removeClass('open');
		});

		$('#wisy-modal-confirm-box button#cancel-action, #wisy-modal-confirm-box button.close').on('click', function () {
			if (typeof callback == 'function') {
				callback(false);
			}
			$('#wisy-modal-confirm-box').removeClass('open');
		});

	}

	// Generate unique ID
	function wisyUniqid(prefix = '', length = 15) {

		var key = '',
			chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		for (var i = 0; i < length; i++) {
			key += chars.charAt(Math.floor(Math.random() * chars.length));
		}

		return prefix + key;

	}

	function wisyCleanAtts(atts, blockName) {
		var cleanAtts = {},
			defAtts = wisyGetBlockDefAtts(blockName);
		if (typeof atts != 'object') {
			return atts;
		}

		defAtts['_hov'] = {};
		defAtts['_lg'] = {};
		defAtts['_md'] = {};
		defAtts['_sm'] = {};

		for (var attName in atts) {
			var attVal = atts[attName];

			if (typeof attVal == 'string' && typeof defAtts[attName] == 'string' && attVal != defAtts[attName]) {
				cleanAtts[attName] = attVal;
			} else if (typeof attVal == 'object' && typeof defAtts[attName] == 'object') {
				var attValJson = JSON.stringify( $.extend(false, defAtts[attName], attVal) ),
					defAttJson = JSON.stringify(defAtts[attName]);

				if (attValJson != defAttJson) {
					cleanAtts[attName] = attVal;
				}
			}
		}

		return cleanAtts;
	}

	function wisyGetBlockDefAtts(blockName) {
		var blockSettings = wisyBuilderBlocks[blockName]['settings'],
			blockDefAtts = {};
		if (typeof blockSettings == 'object') {
			for (var setting in blockSettings) {
				blockDefAtts[setting] = (typeof blockSettings[setting]['default'] != 'undefined') ? blockSettings[setting]['default'] : '';
			}
		}
		blockDefAtts['_md'] = {};
		blockDefAtts['_sm'] = {};

		return blockDefAtts;
	}

	function wisyHistory(action = 'undo') {
		var history = window.wisyHistory,
			back = {},
			blocksArea = $('#wisy-builder-frontend-editor-area > .wisy-editor-rows', window.previewIframe);

		if (action == 'undo' && history.canUndo) {
			back = history.undo.slice(-1)[0];
			history.undo.splice(-1,1);
			history.redo.push( $(blocksArea)[0].innerHTML );
		} else if (action == 'redo' && history.canRedo) {
			back = history.redo.slice(-1)[0];
			history.redo.splice(-1,1);
			history.undo.push( $(blocksArea)[0].innerHTML );
		}

		if (back.length > 0) {
			blocksArea = $(blocksArea).html( back );
			$(blocksArea).find('.wisy-block-sortable-placeholder').remove();
			$(blocksArea).find('.wisy-editor-block').attr('style', '');
			// Update post content textarea
			wisySavePostBlocks();

			// Refresh jquery sortable
			wisyBlocksSortable();
		}

		window.wisyHistory.canUndo = (window.wisyHistory.undo.length > 0) ? true : false;
		window.wisyHistory.canRedo = (window.wisyHistory.redo.length > 0) ? true : false;

		wisyUpdateHistoryBtns(window.wisyHistory);
	}

	function wisyBeforeChangeBlock() {
		var blocksArea = $('#wisy-builder-frontend-editor-area > .wisy-editor-rows', window.previewIframe);
		window.wisyHistory = (typeof window.wisyHistory == 'object') ? window.wisyHistory : {};
		window.wisyHistory.element = blocksArea;
		if (typeof window.wisyHistory.undo != 'object') {
			window.wisyHistory.undo = new Array();
		}
		if (typeof window.wisyHistory.redo != 'object') {
			window.wisyHistory.redo = new Array();
		}
		window.wisyHistory.num = (typeof window.wisyHistory.num == 'number') ? window.wisyHistory.num + 1 : 0;
		window.wisyHistory.undo.push( $(blocksArea)[0].innerHTML );

		window.wisyHistory.canUndo = (window.wisyHistory.undo.length > 0) ? true : false;
		window.wisyHistory.canRedo = (window.wisyHistory.redo.length > 0) ? true : false;

		wisyUpdateHistoryBtns(window.wisyHistory);
	}

	function wisyUpdateHistoryBtns(history = {}) {
		$('#wisy-builder-undo').attr('disabled', !history.canUndo);
		$('#wisy-builder-redo').attr('disabled', !history.canRedo);
	}

	function wisySavePostBlocks(blocks = {}) {
		var postBlocks = [],
			previewIframe = $('#wisy-builder-editor-preview>iframe').contents(),
			blocks = (blocks.length > 0) ? blocks : $('#wisy-builder-frontend-editor-area', previewIframe).findHierarchically('.wisy-editor-block');

		for (var block in blocks) {
			var block = blocks[block],
				blockName = block.element.attr('data-name'),
				blockAtts = JSON.parse(block.element.find('>div>textarea.atts').val());

			var n = postBlocks.length;
			postBlocks[n] = {
				'type': (blockName == 'row' || blockName == 'column') ? blockName : 'widget',
				'name': blockName,
				'atts': blockAtts
			};

			if ((blockName == 'row' || blockName == 'column') && block['children'].length > 0) {
				postBlocks[n].children = wisySavePostBlocks(block['children']);
			}

		}

		// Update textarea
		$(window.parent.document.getElementById('wisy-builder-post-content')).val(JSON.stringify(postBlocks)).trigger('change');

		return postBlocks;
	}

	function wisyEditBlockSidebar(block) {
		var blockName = block.attr('data-name'),
			blockAtts = block.find('textarea.atts').val(),
			settings = window.parent.document.getElementById('wisy-builder-main'),
			blockSettingsCont = $(settings).find('.wisy-editor-block-settings');

		wisyToggleShowSidebar(blockSettingsCont, 'show');

		var wisyBlocks = new WisyBlocks( blockName, blockAtts );
		blockSettingsCont.html( wisyBlocks.settingsRender(block[0]) );
		blockSettingsCont.attr('data-name', blockName);
		blockSettingsCont.attr('data-id', block.attr('id'));
		$('.saturtheme-color-picker').stColorPicker();
	}

	function wisyToggleShowSidebar(blockSettings, action = 'toggle') {
		if (action == 'show') {
			blockSettings.addClass('active').parents('#wisy-builder-main').addClass('settings-sidebar-active');
		} else if (action == 'hide') {
			blockSettings.removeClass('active');
			$('#wisy-builder-main').removeClass('settings-sidebar-active');
		}
	}

	jQuery.fn.wisyAddSingleBlock = function (blockID, blockName, sidebarCont, callback, callbackBefore = function (){}) {
		$(this).wisyUpdateSingleBlock(blockID, blockName, sidebarCont, callback, callbackBefore, true);
	};

	jQuery.fn.wisyUpdateSingleBlock = function (blockID, blockName, sidebarCont, callback, callbackBefore = function (){}, isNewBlock = false) {
		var BlockEl = $(this),
			blockType = wisyBuilderBlocks[ blockName ]['type'],
			settings = $('[name^="wisy_setting["]').serializeFullObject(),
			settingsData = wisyBuilderBlocks[ blockName ]['settings'],
			blockAtts = (isNewBlock) ? {} : Object.assign({}, settings['wisy_setting'][blockName]),
			editorBlockTmp = ($('#wisy_builder_editor_'+blockType+'_template').length > 0) ? $('#wisy_builder_editor_'+blockType+'_template')[0].innerHTML : '',
			blockChildren = (typeof BlockEl.find('.wisy-block-children')[0] != 'undefined') ? BlockEl.find('.wisy-block-children')[0].innerHTML : '';

		blockAtts = wisyEncodeCharacters(blockAtts);

		// Import block's fonts
		for ( var settingName in settingsData )
		{
			var singleSetting = settingsData[ settingName ];
			if ( singleSetting['type'] == 'fontFamily' && typeof blockAtts[settingName] != 'undefined' && blockAtts[settingName].length > 0 )
			{
				var fontHref = 'https://fonts.googleapis.com/css?family=' + blockAtts[settingName].replace(/\s/g, '+') + ':100,200,300,400,500,600,700,800,900';
				$('link', previewIframe).last().after('<link rel="stylesheet" href="' + fontHref + '" type="text/css" media="all" />');
			}
		}

		if (blockType == 'column' && isNewBlock) {
			blockChildren = '';
		} else if (blockType == 'row' && isNewBlock) {
			blockChildren = ($('#wisy_builder_editor_column_template', parent.document).length > 0) ? $('#wisy_builder_editor_column_template', parent.document)[0].innerHTML : '';

			blockChildren = blockChildren
				.replace(/\{block_atts\}/g, JSON.stringify({}))
				.replace(/\{block_frontend\}/g, '')
				.replace(/\{block_children\}/g, '')
				.replace(/\{editor_block_id\}/g, wisyUniqid('wisy_editor_column_'))
				.replace(/\{block_id\}/g, wisyUniqid('wisy_c'));
		}

		blockID = (blockID == '') ? wisyUniqid('wisy_editor_'+blockType+'_') : blockID;

		if (typeof blockAtts._id == 'undefined' || blockAtts._id == '') {
			blockAtts._id = wisyUniqid();
		}
		var blockAttsJson = JSON.stringify( wisyEncodeCharacters( wisyCleanAtts( blockAtts, blockName ) ) );

		if ( typeof callbackBefore == 'function' ) {
			callbackBefore();
		}

		block_id = ( typeof blockAtts['_id'] == 'string' && blockAtts['_id'].length > 0 ) ? 'wisy_' + blockAtts['_id'] : wisyUniqid( 'wisy_' );

		$.ajax({
			url: wisyBuilder.ajaxurl,
			method: 'POST',
			data: {
				'action': 'wisy_get_block_frontend',
				'block_name': blockName,
				'block_atts': blockAtts,
			},
			beforeSend: function( xhr )
			{
				$('#wisy-builder-fullpage-loader').addClass('open');
			},
			success: function( response )
			{

				$('#wisy-builder-fullpage-loader').removeClass('open');

				response = isJSON( response ) ? JSON.parse( response ) : { html: '', block_classes: '', scripts: [] };
				var scripts = ( typeof response['scripts'] == 'object' ) ? response['scripts'] : [];

				scripts.forEach( function (script) {
					var handle = ( typeof script.handle == 'string' ) ? script.handle : '';
					var url = ( typeof script.url == 'string' ) ? script.url : '';
					var inline = ( typeof script.inline == 'string' ) ? script.inline : '';
					var type = ( typeof script.type == 'string' ) ? script.type : '';
					var ver = ( typeof script.ver == 'string' ) ? script.ver : '';

					if ( type == 'css' )
					{
						if ( typeof script.inline == 'string' )
						{
							$('head link', previewIframe).last().after('<style type="text/css">' + inline + '</style>');
						}
						else if ( $('head link#' + handle + '-css', previewIframe).length == 0 )
						{
							$('head link', previewIframe).last().after('<link id="' + handle + '-css" rel="stylesheet" href="' + url + '" type="text/css" media="all" />');
						}
					}
					else if ( type == 'js' )
					{
						if ( typeof script.inline == 'string' )
						{
							$('script', previewIframe).last().after('<script type="text/javascript">' + inline + '</script>');
						}
						else if ( $('script[src^="' + url + '"]', previewIframe).length == 0 )
						{
							$('script', previewIframe).last().after('<script id="' + handle + '-js" src="' + url + '" type="text/javascript"></script>');
						}
					}
				} );

				newBlockHTML = editorBlockTmp
					.replace( /\{block_name\}/g, blockName )
					.replace( /\{block_type\}/g, blockType )
					.replace( /\{block_atts\}/g, blockAttsJson )
					.replace( /\{block_frontend\}/g, response['html'] )
					.replace( /\{block_classes\}/g, response['block_classes'] )
					.replace( /\{editor_block_id\}/g, blockID )
					.replace( /\{block_id\}/g, block_id );

				if ( blockType == 'row' || blockType == 'column' )
				{
					newBlockHTML = newBlockHTML.replace( /\{block_children\}/g, blockChildren );
				}

				//sidebarCont.attr('data-id', blockID);
				if ( typeof callback == 'function' )
				{
					wisyBeforeChangeBlock();
					callback(newBlockHTML, block_id);
					if ( isNewBlock ) {
						wisyActiveBlock( $('#' + block_id, previewIframe) );
					} else {
						$('#' + block_id, previewIframe).addClass('active');
					}
				}
				wisySavePostBlocks();
			}
		});
	};

	jQuery.fn.allValues = function () {
		var values = [];
		$(this).each(function (i, v) {
			values[ values.length ] = $(this).val();
		});
		return values;
	};

	jQuery.fn.findHierarchically = function (selector) {
		var elements = [];
		$(this).find(selector).first().parent().find('>' + selector).each(function (i, v) {
			elements[ elements.length ] = {
				element: $(this),
				children: $(this).findHierarchically(selector)
			};
		});
		return elements;
	};

	jQuery.fn.closestParents = function (selector) {
		var result = jQuery([]);
		// Check to see if there is a selector. If not, then
		// we're just gonna return the parent() call.
		if (!selector) {
			// Since there is no selector, the user simply
			// wants to return the first immediate parent
			// of each element.
			return( this.parent() );
		}
		// Loop over each element in this collection.
		this.each(
			function(index, node) {
				// For each node, we are going to get all the
				// parents that match the given selector; but
				// then, we're only going to add the first
				// one to the ongoing collection.
				result = result.add(
					jQuery(node).parents(selector).first()
				);
			}
		);
		// Return the new collection, pushing it onto the
		// stack (such that end() can be used to return to
		// the original collection).
		return(
			this.pushStack(
				result,
				'closestParents',
				selector
			)
		);
	};

	$.fn.serializeFullObject = function () {
		// Grab a set of name:value pairs from the form dom.
		var set = $(this).serializeArray();
		var output = {};

		for ( var field in set ) {
			if ( ! set.hasOwnProperty(field) ) continue;

			// Split up the field names into array tiers
			var parts = set[field].name.split('[');
			$.map(parts, function (val, i) { parts[i] = val.replace(']', ''); });

			var originalParts = parts;
			// We need to remove any blank parts returned by the regex.
			parts = $.grep(parts, function(n) { return n != ''; });

			// Start ref out at the root of the output object
			var ref = output;

			for ( var segment in parts ) {
				if ( ! parts.hasOwnProperty(segment) ) continue;

				// set key for ease of use.
				var key = parts[segment];
				var value = {};

				// If we're at the last part, the value comes from the original array.
				if ( segment == parts.length - 1 ) {
					value = set[field].value;
				}

				// Create a throwaway object to merge into output.
				var objNew = {};
				if ( originalParts[segment].length == 0 ) {
					var p = {};
					p[key] = value;
					var t = field + segment;
					objNew[t] = p;
				} else {
					objNew[key] = value;
				}

				// Extend output with our temp object at the depth specified by ref.
				$.extend(true, ref, objNew);

				// Reassign ref to point to this tier, so the next loop can extend it.
				ref = ref[key];
			}
		}

		return output;
	};

	$.fn.wisyMediaSelector = function (va = false) {
		var wisyMediaSelecters = document.querySelectorAll('.wisy-media-selecter');

		// Set all variables to be used in scope
		var frame,
			addMediaLink = $(wisyMediaSelecters);

		addMediaLink.off( 'click' );
		// ADD IMAGE LINK
		addMediaLink.on( 'click', function( event ) {

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			// Create a new media frame
			frame = wp.media({
				title: 'Select or Upload Media',
				button: {
					text: 'Select'
				},
				library: {
					type: $(this).attr('data-media-type'),
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});

			frame.off( 'select' );
			// When an image is selected in the media frame...
			frame.on( 'select', function() {

				// Get media attachment details from the frame state
				var attachment = frame.state().get('selection').first().toJSON();

				// Send the attachment URL to our custom image input field.
				//imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

				// Send the attachment URL to our hidden input
				if ($(addMediaLink).hasClass('video-selecter')) {
					event.target.previousSibling.value = attachment.url;
				} else {
					event.target.previousSibling.value = attachment.sizes.full.url;
				}

				$(event.target.previousSibling).trigger('change');
			});

			// Finally, open the modal on click
			frame.open();
		});

	};

	$('body').wisyMediaSelector();

	MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

	var observer = new MutationObserver(function(mutations, observer) {
		// fired when a mutation occurs
		$('select.st-custom-select').wisySelect();
		$('body').wisyMediaSelector(true);
	});

	observer.observe(document, {
		subtree: true,
		attributes: true
	});

});

$('select.st-custom-select').wisySelect();

function wisyTemplaterRender(templateText, varsPrefix, data) {
	var render = wisyTemplater(templateText, varsPrefix);
	return render(data);
}

function wisyTemplater(templateText, varsPrefix) {
	return new Function(
		varsPrefix,
		"var output = " +
		JSON.stringify(templateText)
		.replace(/{{(.+?)}}/g, '"+($1)+"')
		.replace(/<#(.+?)#>/g, '";$1\noutput +="') +
		";return output;"
	);
}

function wisyReplaceGroup(strings, replacements, text) {
	var output = text;
	for (var key in strings) {
		strings[key] = '\\'+strings[key];
		output = output.replace(new RegExp(strings[key], 'g'), replacements[ key ]);
	}
	return output;
}

function wisyEncodeCharacters(string) {
	if (typeof string == 'object') {
		for (var key in string) {
			string[ key ] = wisyEncodeCharacters(string[ key ]);
		}
	} else {
		var characters = ['/', '[', ']', '"', "'", ',', '=', '}', '{', '<', '>', '\r\n', '\n'],
			decoded_characters = ['%2F', '%5B', '%5D', '%5C%22', '%5C%27', '%2C', '%3D', '%7D', '%7B', '%3C', '%3E', '<br/>', '<br/>'];
		string = wisyReplaceGroup(characters, decoded_characters, string);
	}
	return string;
}

function wisyDecodeCharacters(string) {
	if (typeof string == 'object') {
		for (var key in string) {
			string[ key ] = wisyDecodeCharacters(string[ key ]);
		}
	} else {
		var characters = ['/', '[', ']', '&quot;', '&apos;', ',', '=', '}', '{', '<', '>', '\r\n', '\n'],
			decoded_characters = ['%2F', '%5B', '%5D', '%5C%22', '%5C%27', '%2C', '%3D', '%7D', '%7B', '%3C', '%3E', '<br/>', '<br/>'];
		string = wisyReplaceGroup(decoded_characters, characters, string);
	}

	return string;
}

function wisyBrLineHtml(string) {
	if (typeof string == 'object') {
		for (var key in string) {
			string[ key ] = wisyBrLineHtml(string[ key ]);
		}
	} else {
		var characters = ['\r\n', '\n'],
			decoded_characters = ['<br>', '<br>'];
		string = wisyReplaceGroup(characters, decoded_characters, string);
	}
	return string;
}

function isJSON(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}
