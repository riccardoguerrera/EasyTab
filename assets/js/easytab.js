(function($) {

    document.addEventListener("DOMContentLoaded", function() {

        const convalidateForm = (e, form, aiSelected = null, wcTabSelected = null) => {
            e.preventDefault();
            if ((typeof form === null) || (typeof form === 'undefined')) return;
            let isValid = true;
            const inputsGrp1 = form.querySelectorAll('form > table select');
            inputsGrp1.forEach(function (input) {
                if (input.value.trim() === '') {
                    isValid = false;
                    input.style.border = '1px solid red';
                }
            });
            if (aiSelected !== '') {
                const inputsGrp2 = form.querySelectorAll('div.ai-section.' + aiSelected + ':not(div.wc-tab-section) div.' + aiSelected + ' input:not(.not-require), ' +
                    'div.ai-section.' + aiSelected + ':not(div.wc-tab-section) div.' + aiSelected + ' textarea:not(.not-require), ' +
                    'div.ai-section.' + aiSelected + ':not(div.wc-tab-section) div.' + aiSelected + ' select:not(.not-require)');
                inputsGrp2.forEach(function (input) {
                    if (input.value.trim() === '') {
                        isValid = false;
                        input.style.border = '1px solid red';
                    }
                });
            }
            if ((aiSelected !== '') && (wcTabSelected !== '')) {
                const inputsGrp3 = form.querySelectorAll('div.wc-tab-section.' + aiSelected + '.' + wcTabSelected + ' div.' + aiSelected + ' input:not(.not-require), ' +
                    'div.wc-tab-section.' + aiSelected + '.' + wcTabSelected + ' div.' + aiSelected + ' textarea:not(.not-require), ' +
                    'div.wc-tab-section.' + aiSelected + '.' + wcTabSelected + ' div.' + aiSelected + ' select:not(.not-require)');
                inputsGrp3.forEach(function (input) {
                    if (input.value.trim() === '') {
                        isValid = false;
                        input.style.border = '1px solid red';
                    }
                });
            }
            if (isValid) {
                form.removeEventListener('submit', handleFormSubmit);
                HTMLFormElement.prototype.submit.call(form);
            }
        }

        const convalidateFormReset = (input) => {
            input.style.border = '';
        }

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

        const form = document.getElementById('easytab-ai-prompt');

        const wcTabFn = (e, aiSelected) => {
            let wcTabSelected = e.target.dataset.wctab;
            let i, tabContent, tabLinks;
            tabContent = document.getElementsByClassName('wc-tab-section');
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = 'none';
            }
            tabLinks = document.getElementsByClassName('wc-tabs-link');
            for (i = 0; i < tabLinks.length; i++) {
                tabLinks[i].className = tabLinks[i].className.replace(' active', '');
            }
            document.querySelector('div#wc-tab-section-' + wcTabSelected + '-' + aiSelected + '-ai').style.display = 'block';
            e.currentTarget.className += ' active';
        }

        let aiSelected = null;
        let wcTabSelected = null;

        function handleFormSubmit(e) {
            convalidateForm(e, form, aiSelected, wcTabSelected);
        }

        if (document.querySelector('select#ai-select').length > 0) {
            let form = document.querySelector('form');
            aiSelected = document.querySelector('select#ai-select').value;
            if (typeof aiSelected === 'undefined') return;
            aiSelected = aiSelected.replace(/_/g, '-');
            form.classList.add(aiSelected + '-selected-ai');
            form.setAttribute('data-selected-ai', aiSelected);
            form.addEventListener('submit', handleFormSubmit);
            if (aiSelected) $('div.ai-section.' + aiSelected).show();
            document.querySelector('select#ai-select').addEventListener('change', (e) => {
                $('div.ai-section').hide();
                form.setAttribute('class', '');
                form.setAttribute('data-selected-ai', '');
                aiSelected = document.querySelector('select#ai-select').value;
                aiSelected = aiSelected.replace(/_/g, '-');
                form.classList.add(aiSelected + '-selected-ai');
                form.setAttribute('data-selected-ai', aiSelected);
                form.removeEventListener('submit', handleFormSubmit);
                form.addEventListener('submit', handleFormSubmit);
                if (aiSelected) $('div.ai-section.' + aiSelected).show();
            });
            document.querySelectorAll('button.wc-tabs-link')?.forEach((wcTab) => {
                wcTab.addEventListener('click', (e) => {
                    e.preventDefault();
                    wcTabFn(e, aiSelected);
                });
            });
        }

        document.querySelectorAll('div.repeater-field-controls span.add').forEach((addButton) => {
            addButton.addEventListener('click', cloneField);
        });

        document.querySelectorAll('div.repeater-field-controls span.remove').forEach((removeButton) => {
            removeButton.addEventListener('click', removeField);
        });

        document.querySelectorAll('form select, form input, form textarea').forEach((input) => {
            input.addEventListener('change', (e) => {
                convalidateFormReset(input);
            });
            input.addEventListener('keydown', (e) => {
                convalidateFormReset(input);
            });
        });

    });

})(jQuery);