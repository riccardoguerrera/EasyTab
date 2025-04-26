(function($) {

	document.addEventListener("DOMContentLoaded", function() {

		const cloneField = (e) => {
			e.preventDefault();
			let originalField = e.target.closest('div.repeater-field');
			let parentNode = originalField.parentNode;
			let numberRepeaterFields = parentNode.querySelector('input.number-repeater-fields').value;
			if (isNaN(numberRepeaterFields)) numberRepeaterFields = 0;
			numberRepeaterFields = parseInt(numberRepeaterFields , 10) + 1;
			originalField.parentNode.querySelector('input.number-repeater-fields').value = numberRepeaterFields;
			let clonedField = parentNode.querySelector('div.to-clone').cloneNode(true);
			// Optional: Reset input values or make other modifications in the clonedField here
			clonedField.querySelectorAll('input, select, textarea').forEach((input) => {
				let inputAttrName = input.getAttribute('name');
				let inputAttrId = input.getAttribute('id');
				input.setAttribute('name', inputAttrName.replace('%d', numberRepeaterFields));
				input.setAttribute('id', inputAttrId.replace('%d', numberRepeaterFields));
			});
			originalField.insertAdjacentElement('afterend', clonedField);
			clonedField.style.display = 'flex';
			clonedField.classList = originalField.classList;
			// Attach the cloneField function to the 'add' button in the cloned field
			clonedField.querySelector('div.repeater-field-controls span.add').addEventListener('click', cloneField);
			clonedField.querySelector('div.repeater-field-controls span.remove').addEventListener('click', removeField);
		}
		const removeField = (e) => {
			e.preventDefault();
			let originalField = e.target.closest('div.repeater-field');
			let numberRepeaterFields = originalField.parentNode.querySelector('input.number-repeater-fields').value;
			if (isNaN(numberRepeaterFields)) numberRepeaterFields = 0;
			if (parseInt(numberRepeaterFields , 10) === 0) return;
			originalField.parentNode.querySelector('input.number-repeater-fields').value = parseInt(numberRepeaterFields , 10) - 1;
			originalField.remove();
		}

		document.querySelectorAll('div.repeater-field-controls span.add').forEach((addButton) => {
			addButton.addEventListener('click', cloneField);
		});

		document.querySelectorAll('div.repeater-field-controls span.remove').forEach((removeButton) => {
			removeButton.addEventListener('click', removeField);
		});

	});

})(jQuery);

(($) => {

	$(document).ready(() => {
		$('#easytab-pro-eula-content + button').on('click', () => {
			window.location.replace('admin.php?page=easytab&easytab_pro_eula_acceptance=true');
		})
	});

})(jQuery);