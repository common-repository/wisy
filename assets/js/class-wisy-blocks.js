class WisyBlocks {
	constructor(name, atts = []) {
		this.name = name;
		this.atts = atts;
		this.settingTemplate = new WisyBlockSettings();
	}

	settingsRender(block) {
		var html = '',
			sections = this.getSections(),
			settings = this.getSettings(),
			blockAtts = jQuery(block).find('>div>textarea.atts').val();
			blockAtts = isJSON(blockAtts) ? JSON.parse(blockAtts) : {};

		this.atts = blockAtts;
		if (typeof sections != 'object') {
			sections = {};
		}
		if (typeof settings != 'object') {
			settings = {};
		}

		blockAtts = wisyDecodeCharacters(blockAtts);

		html += "<ul class='wisy-block-sections'>";
		var sectionNumber = 1;
		for (var sectionID in sections) {
			var sectionData = sections[ sectionID ],
				sectionClass = (sectionNumber == 1) ? 'active' : '';
			html += "<li class='"+sectionClass+"' data-section='"+sectionID+"'>"+sectionData['title']+"</li>";
			sectionNumber++;
		}
		html += '</ul>';

		var sectionNumber = 1;
		for (var sectionID in sections) {
			var sectionData = sections[ sectionID ],
				sectionGroups = sectionData['groups'],
				sectionClass = 'section section-' + sectionID;
			sectionClass += (sectionNumber == 1) ? ' active' : '';
			html += "<div class='"+sectionClass+"'>";

			var sectionAddedGroups = new Array();
			for (var settingID in settings) {
				var settingData = settings[ settingID ];
				if (settingData['section'] == sectionID) {

					if (('group' in settingData) && typeof sectionData['groups'] == 'object' && typeof sectionData['groups'][ settingData['group'] ] != 'undefined') {
						if ($.inArray(settingData['group'], sectionAddedGroups) > -1) {
							continue;
						}

						sectionAddedGroups.push( settingData['group'] );
						html += this.settingsGroup(
								settingData['group'],
								sectionGroups[ settingData['group'] ],
								sectionID,
								settings
							);
					} else {
						html += this.settingContainer(settingID, settingData);
					}

				}
			}

			html += "</div>";
			sectionNumber++;
		}

		return html;
	}

	settingsGroup(groupID, groupTitle, sectionID, settings) {
		var html = '';

		html += "<div class='setting-group'>" +
			"<span class='setting-group-title'>"+groupTitle+"</span>" +
			"<div class='setting-group-body'>";
			for (var settingID in settings) {
				var settingData = settings[ settingID ];
				if (settingData['section'] == sectionID && settingData['group'] == groupID) {
					html += this.settingContainer(settingID, settingData);
				}
			}
		html += "</div>" +
			"</div>";

		return html;
	}

	settingContainer(settingID, settingData) {
		var fieldData = {
				fieldValue: (settingID in this.atts) ? this.atts[settingID] : (('default' in settingData) ? settingData['default'] : ''),
				fieldName: 'wisy_setting['+this.name+']['+settingID+']',
				fieldID: 'wisy_setting_'+this.name+'_'+settingID
			},
			classes = 'setting';
		fieldData = Object.assign(fieldData, settingData);

		var settingFields = this.singleField(fieldData),
			settingArgs = '';
		if (typeof fieldData['args'] == 'object') {
			if (typeof fieldData['args']['hover'] != 'undefined' && fieldData['args']['hover'] === true) {
				classes += ' setting-hover';

				var atts = (typeof this.atts['_hov'] == 'object') ? this.atts['_hov'] : {};

				fieldData.fieldValue = (settingID in atts) ? atts[settingID] : (('default' in settingData) ? settingData['default'] : '');
				fieldData.fieldName = 'wisy_setting['+this.name+'][_hov]['+settingID+']';
				fieldData.fieldID = 'wisy_setting_'+this.name+'_hov_'+settingID;

				settingFields += this.singleField(fieldData, ' hover-field');
				settingArgs += '<button class="single-arg setting-hover-switcher"><svg class="feather feather-mouse-pointer sc-dnqmqq jxshSx" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-reactid="861"><path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"></path><path d="M13 13l6 6"></path></svg></button>';
			}

			if (typeof fieldData['args']['responsive'] != 'undefined' && fieldData['args']['responsive'] === true) {
				classes += ' setting-responsive';

				var screenSizes = ['md', 'sm'];
				for (var key in screenSizes) {
					var size = screenSizes[key],
						atts = (typeof this.atts['_'+size] == 'object') ? this.atts['_'+size] : {};

					fieldData.fieldValue = (settingID in atts) ? atts[settingID] : '';
					fieldData.fieldName = 'wisy_setting['+this.name+'][_'+size+']['+settingID+']';
					fieldData.fieldID = 'wisy_setting_'+this.name+'_'+size+'_'+settingID;

					settingFields += this.singleField(fieldData, ' responsive-field-'+size);
				}

				settingArgs += '<button class="button single-arg wisy-builder-responsive setting-responsive-switcher">'+
					'<i class="feather icon-monitor"></i>'+
					'<ul class="devices-list hidden">'+
						'<li data-size="lg" class="active">'+
							'<i class="feather icon-monitor"></i>'+
						'</li>'+
						'<li data-size="md">'+
							'<i class="feather icon-tablet"></i>'+
						'</li>'+
						'<li data-size="sm">'+
							'<i class="feather icon-smartphone"></i>'+
						'</li>'+
					'</ul>'+
				'</button>';
			}
		}

		if (fieldData.type == 'hidden') {
			classes += ' hidden';
		}

		var html = "<div class='"+classes+"'>"+
			"<span class='setting-title'>"+fieldData.title+"</span>"+
				"<div class='setting-args'>"+settingArgs+"</div>";
				html += settingFields;
				if (typeof fieldData.alerts == 'object') {
					html += "<div class='setting-alerts'>";
						for (var alertType in fieldData.alerts) {
							html += "<span class='setting-alert alert-"+alertType+"'>"+fieldData.alerts[alertType]+"</span>";
						}
					html += "</div>";
				}
			html += '</div>';

		return html;
	}

	singleField(fieldData, classes = '') {
		var html = "<div class='setting-body"+classes+"'>" +
				this.settingTemplate.get(fieldData.type, fieldData) +
			"</div>";

		return html;
	}

	getTitle() {
		return this.getBlockData('title');
	}

	getSections() {
		return this.getBlockData('sections');
	}

	getSettings() {
		return this.getBlockData('settings');
	}

	getTemplate() {
		return this.getBlockData('template');
	}

	getBlockData(data = '') {
		if (typeof wisyBuilderBlocks[this.name] == 'undefined') {
			return [];
		}
		return (data != '') ? wisyBuilderBlocks[this.name][data] : wisyBuilderBlocks[this.name];
	}
}
