/**
 * Group Quotation helpers — unique transport row IDs, Select2-safe cell reads,
 * and datepicker init that does not stack widgets.
 */
(function (window, $) {
	'use strict';

	function gqCellControl(cell) {
		if (!cell) {
			return null;
		}
		if (typeof getCellFormControl === 'function') {
			return getCellFormControl(cell);
		}
		var $el = $(cell).find('select, input, textarea').first();
		return $el.length ? $el[0] : null;
	}

	function gqCellValue(cell) {
		var el = gqCellControl(cell);
		if (!el) {
			return '';
		}
		var val = $(el).val();
		if (val === null || val === undefined) {
			return '';
		}
		return String(val);
	}

	function gqSetCellValue(cell, value) {
		var el = gqCellControl(cell);
		if (!el) {
			return;
		}
		var $el = $(el);
		if ($el.data('select2')) {
			$el.val(value).trigger('change');
		} else {
			el.value = value == null ? '' : value;
		}
	}

	function gqRowChecked(row) {
		if (!row || !row.cells || !row.cells[0]) {
			return false;
		}
		var chk = $(row.cells[0]).find('input[type="checkbox"]').first()[0] || gqCellControl(row.cells[0]);
		return !!(chk && chk.checked);
	}

	function gqSelectedOptgroup(cell) {
		var el = gqCellControl(cell);
		if (!el) {
			return '';
		}
		var $opt = $(el).find('option:selected').first();
		var group = $opt.parent('optgroup').attr('value') || $opt.parent('optgroup').attr('label') || '';
		if (!group && el.value && String(el.value).indexOf('-') > -1) {
			group = String(el.value).split('-')[0];
		}
		return String(group).toLowerCase().replace(' name', '');
	}

	function gqSelectedText(cell) {
		var el = gqCellControl(cell);
		if (!el) {
			return '';
		}
		if (el.tagName === 'SELECT') {
			return $.trim($(el).find('option:selected').text() || '');
		}
		return gqCellValue(cell);
	}

	function gqNextRowIndex(table) {
		var max = 0;
		$(table).find('tr').each(function () {
			$(this).find('[id]').each(function () {
				var m = String(this.id).match(/(\d+)(?:_[a-z]+)?$/i);
				if (m) {
					max = Math.max(max, parseInt(m[1], 10) || 0);
				}
			});
		});
		return max + 1;
	}

	function gqInitDatepicker($els, withTime) {
		if (!$els || !$els.length) {
			return;
		}
		var opts = withTime
			? { format: 'd-m-Y H:i', parentID: 'body', fixed: true, scrollInput: false }
			: { timepicker: false, format: 'd-m-Y' };
		$els.each(function () {
			var $el = $(this);
			if ($el.data('xdsoft_datetimepicker')) {
				$el.datetimepicker('destroy');
			}
			$el.datetimepicker(opts);
			$el.addClass('form-control');
		});
	}

	function gqSetSelectOption($select, value, text, optgroupValue) {
		if (!$select || !$select.length || !value) {
			return;
		}
		var label = text || value;
		var groupVal = optgroupValue || '';
		var groupLabel = groupVal ? (groupVal.charAt(0).toUpperCase() + groupVal.slice(1) + ' Name') : '';
		if ($select.hasClass('select2-hidden-accessible')) {
			$select.select2('destroy');
		}
		$select.empty();
		if (groupVal) {
			$select.append(
				'<optgroup value="' + groupVal + '" label="' + groupLabel + '">' +
					'<option value="' + value + '" selected>' + label + '</option>' +
				'</optgroup>'
			);
		} else {
			$select.append(new Option(label, value, true, true));
		}
	}

	function gqAddTransportRow(tableId) {
		var table = document.getElementById(tableId);
		if (!table) {
			return null;
		}
		var src = table.rows[table.rows.length - 1];
		if (!src) {
			return null;
		}
		var idx = gqNextRowIndex(table);
		var suffix = tableId.indexOf('_u') !== -1 ? '_u' : '';
		var $src = $(src);
		var $row = $src.clone();

		$row.find('.select2-container').remove();
		$row.find('select').each(function () {
			$(this).removeClass('select2-hidden-accessible').removeAttr('data-select2-id').show();
			$(this).find('option').prop('selected', false);
			if (this.options && this.options.length) {
				this.selectedIndex = 0;
			}
		});
		$row.find('input[type="checkbox"]').prop('checked', true).prop('disabled', false);
		$row.find('input:not([type="checkbox"]):not([type="hidden"])').val('');

		function rename(el, base) {
			if (!el) {
				return;
			}
			el.id = base + idx + suffix;
			if (el.name) {
				el.name = base + idx + suffix;
			}
		}

		var cells = $row[0].cells;
		if (cells[0]) {
			var chk = $(cells[0]).find('input[type="checkbox"]')[0];
			if (chk) {
				chk.id = 'chk_transport' + idx;
				chk.name = chk.id;
				$(cells[0]).find('label.css-label').attr('for', chk.id);
			}
		}
		if (cells[1]) {
			$(cells[1]).find('input').val(idx);
		}
		rename(gqCellControl(cells[2]), 'transport_vehicle_name');
		rename(gqCellControl(cells[3]), 'transport_start_date');
		rename(gqCellControl(cells[4]), 'transport_end_date');
		rename(gqCellControl(cells[5]), 'transport_pickup_from');
		rename(gqCellControl(cells[6]), 'transport_drop_to');
		rename(gqCellControl(cells[7]), 'transport_service_duration');
		rename(gqCellControl(cells[8]), 'transport_no_vehicles');
		if (cells[9]) {
			var hid = gqCellControl(cells[9]);
			if (hid) {
				hid.id = 'transport_entry_id' + idx + suffix;
				hid.name = hid.id;
				hid.value = '';
			}
		}

		var startEl = gqCellControl(cells[3]);
		var endEl = gqCellControl(cells[4]);
		if (startEl) {
			startEl.setAttribute('onchange', "get_to_date(this.id,'" + (endEl ? endEl.id : '') + "');");
		}
		if (endEl && startEl) {
			endEl.setAttribute('onchange', "validate_validDate('" + startEl.id + "','" + endEl.id + "');");
		}

		$(table).append($row);

		var $new = $(table.rows[table.rows.length - 1]);
		var $vehicle = $new.find('select[id^="transport_vehicle_name"]');
		var $duration = $new.find('select[id^="transport_service_duration"]');
		var $pickup = $new.find('select[id^="transport_pickup_from"]');
		var $drop = $new.find('select[id^="transport_drop_to"]');

		$vehicle.select2();
		$duration.select2();
		if (typeof initAllVehicleSelectAddNew === 'function') {
			initAllVehicleSelectAddNew('#' + tableId);
		}
		if (typeof destinationLoading === 'function') {
			destinationLoading($pickup, 'Pickup Location');
			destinationLoading($drop, 'Drop-off Location');
		}
		gqInitDatepicker($new.find('.app_datepicker'), false);
		return $new[0];
	}

	function gqFillTransportRow(row, data) {
		if (!row || !data) {
			return;
		}
		var vehicleEl = gqCellControl(row.cells[2]);
		if (vehicleEl && data.vehicle_id) {
			$(vehicleEl).val(String(data.vehicle_id)).trigger('change');
		}
		if (data.start_date) {
			gqSetCellValue(row.cells[3], data.start_date);
		}
		if (data.end_date) {
			gqSetCellValue(row.cells[4], data.end_date);
		}
		var $pickup = $(gqCellControl(row.cells[5]));
		var $drop = $(gqCellControl(row.cells[6]));
		gqSetSelectOption($pickup, data.pickup_value, data.pickup_location, data.pickup_type);
		gqSetSelectOption($drop, data.drop_value, data.drop_location, data.drop_type);
		if (typeof destinationLoading === 'function') {
			destinationLoading($pickup, 'Pickup Location');
			destinationLoading($drop, 'Drop-off Location');
		}
		if (data.service_duration) {
			var durEl = gqCellControl(row.cells[7]);
			if (durEl) {
				var $dur = $(durEl);
				var match = $dur.find('option').filter(function () {
					return $(this).text() === data.service_duration || String(this.value) === String(data.service_duration);
				}).first();
				if (match.length) {
					$dur.val(match.val()).trigger('change');
				} else {
					$dur.prepend(new Option(data.service_duration, data.service_duration, true, true)).trigger('change');
				}
			}
		}
		if (data.no_vehicles !== undefined) {
			gqSetCellValue(row.cells[8], data.no_vehicles);
		}
		var chk = $(row.cells[0]).find('input[type="checkbox"]')[0];
		if (chk) {
			chk.checked = true;
		}
	}

	function gqCollectTransportRows(tableId) {
		var table = document.getElementById(tableId);
		var out = {
			vehicle: [],
			start: [],
			end: [],
			pickup: [],
			pickup_type: [],
			drop: [],
			drop_type: [],
			duration: [],
			vehicles: [],
			entry_id: []
		};
		if (!table) {
			return out;
		}
		for (var i = 0; i < table.rows.length; i++) {
			var row = table.rows[i];
			if (!gqRowChecked(row)) {
				continue;
			}
			out.vehicle.push(gqCellValue(row.cells[2]));
			out.start.push(gqCellValue(row.cells[3]));
			out.end.push(gqCellValue(row.cells[4]));
			out.pickup.push(gqCellValue(row.cells[5]));
			out.pickup_type.push(gqSelectedOptgroup(row.cells[5]));
			out.drop.push(gqCellValue(row.cells[6]));
			out.drop_type.push(gqSelectedOptgroup(row.cells[6]));
			out.duration.push(gqSelectedText(row.cells[7]));
			out.vehicles.push(row.cells[8] ? gqCellValue(row.cells[8]) : '');
			out.entry_id.push(row.cells[9] ? gqCellValue(row.cells[9]) : '');
		}
		return out;
	}

	function gqCollectPlaneFromRow(row) {
		return {
			from_sector: gqCellValue(row.cells[2]),
			to_sector: gqCellValue(row.cells[3]),
			airline: gqCellValue(row.cells[4]),
			plane_class: gqCellValue(row.cells[5]),
			depart: gqCellValue(row.cells[6]),
			arrival: gqCellValue(row.cells[7]),
			from_city: row.cells[8] ? gqCellValue(row.cells[8]) : '',
			to_city: row.cells[9] ? gqCellValue(row.cells[9]) : '',
			entry_id: row.cells[10] ? gqCellValue(row.cells[10]) : ''
		};
	}

	function gqCollectTrainFromRow(row) {
		return {
			from: gqCellValue(row.cells[2]),
			to: gqCellValue(row.cells[3]),
			train_class: gqCellValue(row.cells[4]),
			depart: gqCellValue(row.cells[5]),
			arrival: gqCellValue(row.cells[6]),
			entry_id: row.cells[7] ? gqCellValue(row.cells[7]) : ''
		};
	}

	window._gqTravelStayKey = window._gqTravelStayKey || null;
	function gqShouldReloadTravelStay(tourId, groupId) {
		var key = String(tourId || '') + ':' + String(groupId || '');
		if (window._gqTravelStayKey === key && key !== ':') {
			return false;
		}
		window._gqTravelStayKey = key;
		return true;
	}

	window.gqCellControl = gqCellControl;
	window.gqCellValue = gqCellValue;
	window.gqSetCellValue = gqSetCellValue;
	window.gqRowChecked = gqRowChecked;
	window.gqSelectedOptgroup = gqSelectedOptgroup;
	window.gqSelectedText = gqSelectedText;
	window.gqNextRowIndex = gqNextRowIndex;
	window.gqInitDatepicker = gqInitDatepicker;
	window.gqSetSelectOption = gqSetSelectOption;
	window.gqAddTransportRow = gqAddTransportRow;
	window.gqFillTransportRow = gqFillTransportRow;
	window.gqCollectTransportRows = gqCollectTransportRows;
	window.gqCollectPlaneFromRow = gqCollectPlaneFromRow;
	window.gqCollectTrainFromRow = gqCollectTrainFromRow;
	window.gqShouldReloadTravelStay = gqShouldReloadTravelStay;

	window.addTransportRowSave = function () {
		gqAddTransportRow('tbl_group_tour_quotation_transport');
	};
	window.addTransportRow = function () {
		gqAddTransportRow('tbl_group_tour_quotation_transport_u');
	};
})(window, jQuery);
