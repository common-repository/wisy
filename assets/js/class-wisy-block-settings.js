class WisyBlockSettings {
	constructor() {}

	get(type, settingData) {
		//return wisyTemplaterRender( this.getSettingTmp(type), 'data', settingData );
		return this.getSettingTmp(type, settingData);
	}

	getSettingTmp(type, data) {
		if (typeof this[type + 'Field'] == 'function') {
			return this[type + 'Field'](data);
		} else {
			return '';
		}
	}

	textField(data) {
		data.placeholder = (typeof data.placeholder == 'string') ? data.placeholder : '';
		return '<input type="text" name="'+data.fieldName+'" id="'+data.fieldID+'" placeholder="'+data.placeholder+'" value="'+data.fieldValue+'">';
	}

	hiddenField(data) {
		return '<input type="hidden" class="hidden" name="'+data.fieldName+'" id="'+data.fieldID+'" value="'+data.fieldValue+'">';
	}

	textareaField(data) {
		return '<textarea name="'+data.fieldName+'" id="'+data.fieldID+'">'+data.fieldValue+'</textarea>';
	}

	colorField(data) {
		return '<input type="text" class="saturtheme-color-picker" name="'+data.fieldName+'" id="'+data.fieldID+'" value="'+data.fieldValue+'" data-classes="size-sm">';
	}

	imageField(data) {
		return '<input type="url" name="'+data.fieldName+'" id="'+data.fieldID+'" value="'+data.fieldValue+'">'+
			'<button type="button" class="wisy-media-selecter image-selecter">'+data.button_txt+'</button>';
	}

	videoField(data) {
		return '<input type="url" name="'+data.fieldName+'" id="'+data.fieldID+'" value="'+data.fieldValue+'">'+
			'<button type="button" class="wisy-media-selecter video-selecter" data-media-type="'+data.media_type+'">'+data.button_txt+'</button>';
	}

	selectField(data) {
		var html = '<select class="st-custom-select" name="'+data.fieldName+'" id="'+data.fieldID+'">';
			for (var value in data.values) {
				html += '<option value="'+value+'"';
				if (value == data.fieldValue) {
					html += ' selected';
				}
				html += '>'+data.values[value]+'</option>';
			}
		html += '</select>';
		return html;
	}

	boxSidesField(data) {
		var html = '';
		data.fieldValue = isNaN(data.fieldValue) ? data.fieldValue : data.fieldValue+'';
		var matchVals = data.fieldValue.match( new RegExp(/(auto)|((\-|)+[0-9.]+(px|%|em|))/, 'g') );
		matchVals = (typeof matchVals == 'object' && isNaN(matchVals)) ? matchVals : [''];
		switch (matchVals.length) {
			case 1:
				matchVals = [matchVals[0], matchVals[0], matchVals[0], matchVals[0]];
				break;
			case 2:
				matchVals = [matchVals[0], matchVals[1], matchVals[0], matchVals[1]];
				break;
			case 3:
				matchVals = [matchVals[0], matchVals[1], matchVals[2], matchVals[1]];
				break;
		}
		html += '<div class="wisy-builder-boxsides-fields">'+
			'<div class="group-fields">';
				var sides = ['top', 'right', 'bottom', 'left'];
				for (var key in sides) {
					html += '<input type="text" value="'+matchVals[key]+'" placeholder="'+wisyL10n[sides[key]]+'" title="'+wisyL10n[sides[key]]+'" class="'+sides[key]+'">';
				}
			html += '</div>'+
			'<input type="hidden" id="'+data.fieldID+'" name="'+data.fieldName+'" value="'+data.fieldValue+'">'+
		'</div>';
		return html;
	}

	borderRadiusField(data) {
		var html = '';
		data.fieldValue = isNaN(data.fieldValue) ? data.fieldValue : data.fieldValue+'';
		var matchVals = data.fieldValue.match( new RegExp(/(auto)|((\-|)+[0-9.]+(px|%|em|))/, 'g') );
		matchVals = (typeof matchVals == 'object' && isNaN(matchVals)) ? matchVals : [''];
		switch (matchVals.length) {
			case 1:
				matchVals = [matchVals[0], matchVals[0], matchVals[0], matchVals[0]];
				break;
			case 2:
				matchVals = [matchVals[0], matchVals[1], matchVals[0], matchVals[1]];
				break;
			case 3:
				matchVals = [matchVals[0], matchVals[1], matchVals[2], matchVals[1]];
				break;
		}
		html += '<div class="wisy-builder-boxsides-fields">'+
			'<div class="group-fields">';
				var corners = ['t-l', 't-r', 'b-r', 'b-l'];
				var cornersTitles = {
					't-l': 'Top-Left',
					't-r': 'Top-Right',
					'b-r': 'Bottom-Right',
					'b-l': 'Bottom-Left'
				};
				for (var key in corners) {
					html += '<input type="text" value="'+matchVals[key]+'" placeholder="'+cornersTitles[corners[key]]+'" title="'+cornersTitles[corners[key]]+'" class="'+corners[key]+'">';
				}
			html += '</div>'+
			'<input type="hidden" id="'+data.fieldID+'" name="'+data.fieldName+'" value="'+data.fieldValue+'">'+
		'</div>';
		return html;
	}

	checkboxField(data) {
		var html = '<input type="checkbox" name="'+data.fieldName+'" class="custom-switch-checkbox" id="'+data.fieldID+'"';
		if (typeof data.value == 'string') {
			html += ' value="'+data.value+'"';
			html += (data.value == data.fieldValue) ? ' checked' : '';
		}
		html += '>'+
		'<label for="'+data.fieldID+'" class="custom-switch"></label>';
		return html;
	}

	switchField(data) {
		var html = '<input type="checkbox" name="'+data.fieldName+'" class="custom-switch-checkbox" id="'+data.fieldID+'"';
		if (typeof data.value == 'string') {
			html += ' value="'+data.value+'"';
			html += (data.value == data.fieldValue) ? ' checked' : '';
		}
		html += '>'+
		'<label for="'+data.fieldID+'" class="custom-switch"></label>'+
		'<span class="text">';
			if (typeof data.text.enabled == 'string') {
				html += '<span class="enabled-text">'+data.text.enabled+'</span>';
			}
			if (typeof data.text.disabled == 'string') {
				html += '<span class="disabled-text">'+data.text.disabled+'</span>';
			}
		html += '</span>';
		return html;
	}

	linkField(data) {
		var html = '<div class="wisy-builder-link-field">'+
			'<input type="url" name="'+data.fieldName+'[url]" value="'+data.fieldValue.url+'" id="">'+
			'<input type="checkbox" name="'+data.fieldName+'[new_window]" id=""';
			html += (data.fieldValue.new_window == 'on') ? ' checked' : '';
			html += '>New Window'+
			'<input type="checkbox" name="'+data.fieldName+'[nofollow]" id=""';
			html += (data.fieldValue.nofollow == 'on') ? ' checked' : '';
			html += '>No Follow'+
		'</div>';
		return html;
	}

	fontFamilyField(data) {
		var html = '<select class="st-custom-select" name="'+data.fieldName+'" id="'+data.fieldID+'">'+
			'<option value="">'+wisyL10n.default+'</option>';
			for (var fontName in wisyBuilderGoogleFonts) {
				html += '<option value="'+fontName+'" style="font-family:'+fontName+';"';
				if (fontName == data.fieldValue) {
					html += ' selected';
				}
				html += '>'+fontName+'</option>';
			}
		html += '</select>';
		return html;
	}

	fontWeightField(data) {
		var html = '<select name="'+data.fieldName+'" id="'+data.fieldID+'">'+
			'<option value="">'+wisyL10n.default+'</option>';
			var fontWeights = ['100','200','300','400','500','600','700','800','900'];
			for (var key in fontWeights) {
				html += '<option value="'+fontWeights[key]+'"';
					if (fontWeights[key] == data.fieldValue) {
						html += ' selected';
					}
				html += '>'+fontWeights[key]+'</option>';
			}
		html += '</select>';
		return html;
	}

	fontSizeField(data) {
		var html = '<select name="'+data.fieldName+'" id="'+data.fieldID+'">'+
			'<option value="">'+wisyL10n.default+'</option>';
			for (var i = 1; i <= 100; i++) {
				html += '<option value="'+i+'"';
					if (i == data.fieldValue) {
						html += ' selected';
					}
				html += '>'+i+'px</option>';
			}
		html += '</select>';
		return html;
	}

	lineHeightField(data) {
		var html = '<select name="'+data.fieldName+'" id="'+data.fieldID+'">'+
			'<option value="">'+wisyL10n.default+'</option>';
			for (var i = 1; i <= 15; i++) {
				html += '<option value="'+i+'"';
					html += (i == data.fieldValue) ? ' selected' : '';
				html += '>'+i+'</option>';
				'<option value="'+i+'.5"';
					html += ((i+0.5) == data.fieldValue) ? ' selected' : '';
				html += '>'+i+'.5</option>';
			}
		html += '</select>';
		return html;
	}

	iconField(data) {
		var html = '<select class="st-custom-select" name="' + data.fieldName + '" id="' + data.fieldID + '">'+
			'<option value=""></option>';
			for ( var i in wisyIcons )
			{
				html += '<option value="' + wisyIcons[ i ] + '"';
				if ( wisyIcons[ i ] == data.fieldValue ) {
					html += ' selected="selected"';
				}
				html += ' data-before="<i class=&quot;' + wisyIcons[ i ] + '&quot;></i>"> ' + wisyIcons[ i ] + '</option>';
			}
		html += '</select>';
		return html;
	}
} // end class
