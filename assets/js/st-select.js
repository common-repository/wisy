let stSelectOptionCount = 1;
// Wisy custom select
$.fn.wisySelect = function () {
	var context = $(this).context;

	var resizeOptionsBox = function (selectCont) {
		var selectDistance = $(selectCont).find('.selected')[0].getBoundingClientRect();

		selectDistance.viewport = ($(context.context).is('iframe')) ? $(context.context).height() : Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
		var optionsBoxMaxHeight = selectDistance.viewport,
			optionsBoxPosition = {};

		if ((selectDistance.viewport - selectDistance.bottom) >= selectDistance.top) {
			optionsBoxMaxHeight = optionsBoxMaxHeight - selectDistance.bottom;
			optionsBoxPosition.top = selectDistance.bottom;
			optionsBoxPosition.bottom = 'inherit';
		} else {
			optionsBoxMaxHeight = selectDistance.top;
			optionsBoxPosition.top = 'inherit';
			optionsBoxPosition.bottom = selectDistance.viewport - selectDistance.top;
		}

		selectCont.find('.select .options-box').css({
			'width': selectDistance.width+'px',
			'max-height': optionsBoxMaxHeight+'px',
			'top': optionsBoxPosition.top,
			'bottom': optionsBoxPosition.bottom,
			'left': selectDistance.left+'px'
		});
	};

	// Custom select
	$('select.st-custom-select', context).each(function () {
		var $this = $(this)[0],
			selectHTML = $($this.outerHTML).removeClass('st-custom-select').addClass('hidden').outerHTML(),
			selectOptions = $($this.outerHTML).find('option'), // findHierarchically
			selectedOption = $($this).find('option[value="'+$this.value+'"]')[0],
			customHTML;

		customHTML = '<div class="st-custom-select">';
			customHTML += '<div class="select">';
				customHTML += '<div class="selected"><span>'+selectedOption.innerText+'</span><i class="feather icon-chevron-down"></i></div>';
				customHTML += '<div class="options-box">';
					customHTML += '<ul>';
						for (var i = 0; i <= (selectOptions.length - 1); i++) {
							var el = (typeof selectOptions[i] == 'object') ? selectOptions[i] : {};
							var text = el.innerText, // element[0]
								value = el.value;
							customHTML += '<li';
							customHTML += (selectedOption.value == value) ? ' class="active"' : '';
							customHTML += ' data-value="'+value+'">'+text+'</li>';
						}
					customHTML += '</ul>';
				customHTML += '</div>';
			customHTML += '</div>';
			customHTML += selectHTML;
		customHTML += '</div>';
		$(this).replaceWith(customHTML);
	});

	$(window).on('resize', function () {
		$('div.st-custom-select', context).each(function (e) {
			resizeOptionsBox($(this));
		});
	});

	// Open/Close select
	$('.st-custom-select .selected', context).on('click', function (e) {
		var $Elem = $(e.target),
			selectCont = $Elem.closest('.st-custom-select'),
			wisySelects = $('.st-custom-select', context);

		resizeOptionsBox(selectCont);

		wisySelects.removeClass('open');
		selectCont.addClass('open');
	});

	$(context).on('click', '.st-custom-select>.select>.options-box>ul>li', function (e) {
		var $Elem = $(e.target),
			selectCont = $Elem.closestParents('.st-custom-select'),
			wisySelects = $(context).find('.st-custom-select');

		var option = {
			val: $Elem.attr('data-value'),
			txt: $Elem[0].innerText
		};

		$Elem.closestParents('.select').find('>.selected>span').text(option.txt);
		selectCont.find('.select .options-box ul li').removeClass('active');
		selectCont.find('.select .options-box ul li[data-value="'+option.val+'"]').addClass('active');
		selectCont.removeClass('open');

		if (stSelectOptionCount == 1) {
			selectCont.find('>select').val(option.val).trigger('change');
		}

		stSelectOptionCount++;
	});

	stSelectOptionCount = 1;

	// Detect all clicks on the document
	$(context).on('click', function(event) {
		var wisySelects = $(context).find('.st-custom-select');

		// If user clicks inside the element, do nothing
		if (event.target.closest('.st-custom-select')) {
			return;
		}

		// If user clicks outside the element, hide it!
		wisySelects.removeClass('open');
	});

	$(context).on('scroll', function () {
		$(context).find('.st-custom-select.open').removeClass('open');
	});

};
