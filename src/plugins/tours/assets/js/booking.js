/**
 * @link
 * @copyright
 * @license
 */

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class Catalogs_Plugins_Tours_CatalogToursBooking {
    /**
     * @type {HTMLElement}
     */
    addTouristButton = document.querySelector('[data-action="add-tourist"]');

    /**
     * @type {NodeListOf<HTMLElement>}
     */
    deleteTouristButtons = document.querySelectorAll('[data-action="delete-tourist"]');

    /**
     * @type {HTMLElement}
     */
    cardContainer = document.querySelector('.booking-cards');

    /**
     * @type {integer}
     */
    touristCounter = 0;

    /**
     * @var {HTMLElement}
     */
    priceDisplay = document.querySelector('.booking-price');

    /**
     * Description
     */
    constructor() {
        this.touristCounter = document.querySelectorAll('.card-tourist').length;

        this.deleteTouristButtons.forEach(element => {
            element.addEventListener('click', () => {
                this.removeToursit(element.dataset.index);
                this.updatePrice();
            });
        });

        if (this.addTouristButton) {
            this.addTouristButton.addEventListener('click', () => {
                this.addTouristForm();
                this.updatePrice();
            });
        }
    }

    /**
     * Description
     */
    addTouristForm() {
        // @todo check date capacity

        const index = this.getTouristIndex();
        const id = this.getTouristId(index);

        const wrapper = document.createElement('div');
        wrapper.dataset.index = index;
        wrapper.id = id;
        wrapper.classList.add('col-lg-6', 'card-tourist');

        const card = document.createElement('div');
        card.classList.add('card', 'mt-3');

        const cardHeader = document.createElement('div');
        cardHeader.classList.add('card-header');
        cardHeader.textContent = 'Информация о туристе';

        const cardBody = document.createElement('div');
        cardBody.classList.add('card-body');

        const formRow = document.createElement('div');
        formRow.classList.add('form-row');

        const columnName = document.createElement('div');
        columnName.classList.add('col-6');

        const columnSurname = document.createElement('div');
        columnSurname.classList.add('col-6');

        const columnPatronymic = document.createElement('div');
        columnPatronymic.classList.add('col-12');

        const columnBirthday = document.createElement('div');
        columnBirthday.classList.add('col-6');

        const columnSex = document.createElement('div');
        columnSex.classList.add('col-6');

        const columnNote = document.createElement('div');
        columnNote.classList.add('col-12');

        const inputContainerName = document.createElement('div');
        inputContainerName.classList.add('form-group', 'position-relative', `field-person-${index}-name`);

        const inputContainerSurname = document.createElement('div');
        inputContainerSurname.classList.add('form-group', 'position-relative', `field-person-${index}-surname`);

        const inputContainerPatronymic = document.createElement('div');
        inputContainerPatronymic.classList.add('form-group', 'position-relative', `field-person-${index}-patronymic`);

        const inputContainerBirthday = document.createElement('div');
        inputContainerBirthday.classList.add('form-group', 'position-relative', `field-person-${index}-birthday`);

        const inputContainerSex = document.createElement('div');
        inputContainerSex.classList.add('form-group', 'position-relative', `field-person-${index}-sex`);

        const inputContainerNote = document.createElement('div');
        inputContainerNote.classList.add('form-group', 'position-relative', `field-person-${index}-note`);

        const inputName = document.createElement('input');
        inputName.classList.add('form-control');
        inputName.id = `person-${index}-name`;
        inputName.name = `Person[${index}][name]`;
        inputName.type = 'text';
        inputName.placeholder = 'Имя';

        const inputSurname = document.createElement('input');
        inputSurname.classList.add('form-control');
        inputSurname.id = `person-${index}-surname`;
        inputSurname.name = `Person[${index}][surname]`;
        inputSurname.type = 'text';
        inputSurname.placeholder = 'Фамилия';

        const inputPatronymic = document.createElement('input');
        inputPatronymic.classList.add('form-control');
        inputPatronymic.id = `person-${index}-patronymic`;
        inputPatronymic.name = `Person[${index}][patronymic]`;
        inputPatronymic.type = 'text';
        inputPatronymic.placeholder = 'Отчество';

        const inputBirthday = document.createElement('input');
        inputBirthday.classList.add('form-control');
        inputBirthday.id = `person-${index}-birthday`;
        inputBirthday.name = `Person[${index}][birthday]`;
        inputBirthday.type = 'text';
        inputBirthday.placeholder = 'День рождения';

        const inputSex = document.createElement('select');
        inputSex.classList.add('form-control', 'custom-select');
        inputSex.id = `person-${index}-sex`;
        inputSex.name = `Person[${index}][sex]`;
        inputSex.placeholder = 'Пол';
        inputSex.options.add(new Option('Мужской', 1));
        inputSex.options.add(new Option('Женский', 0));

        const inputNote = document.createElement('textarea');
        inputNote.classList.add('form-control');
        inputNote.id = `person-${index}-note`;
        inputNote.name = `Person[${index}][note]`;
        inputNote.placeholder = 'Примечание';

        const labelName = document.createElement('label');
        labelName.for = `Person[${index}][name]`;
        labelName.textContent = 'Имя';

        const labelSurname = document.createElement('label');
        labelSurname.for = `Person[${index}][surname]`;
        labelSurname.textContent = 'Фамилия';

        const labelPatronymic = document.createElement('label');
        labelPatronymic.for = `Person[${index}][patronymic]`;
        labelPatronymic.textContent = 'Отчество';

        const labelBirthday = document.createElement('label');
        labelBirthday.for = `Person[${index}][birthday]`;
        labelBirthday.textContent = 'День рождения';

        const tooltipName = document.createElement('div');
        tooltipName.classList.add('invalid-tooltip');

        const tooltipSurname = document.createElement('div');
        tooltipSurname.classList.add('invalid-tooltip');

        const tooltipPatronymic = document.createElement('div');
        tooltipPatronymic.classList.add('invalid-tooltip');

        const tooltipBirthday = document.createElement('div');
        tooltipBirthday.classList.add('invalid-tooltip');

        const tooltipSex = document.createElement('div');
        tooltipSex.classList.add('invalid-tooltip');

        const tooltipNote = document.createElement('div');
        tooltipNote.classList.add('invalid-tooltip');

        const removeToursit = document.createElement('button');
        removeToursit.classList.add('btn', 'btn-secondary');
        removeToursit.dataset.index = index;
        removeToursit.type = 'button';
        removeToursit.textContent = 'Удалить';
        removeToursit.addEventListener('click', () => {
            this.removeToursit(index);
            this.updatePrice();
        });

        wrapper.appendChild(card);
        card.appendChild(cardHeader);
        card.appendChild(cardBody);

        cardBody.appendChild(formRow);
        cardBody.appendChild(removeToursit);

        formRow.appendChild(columnName);
        formRow.appendChild(columnSurname);
        formRow.appendChild(columnPatronymic);
        formRow.appendChild(columnBirthday);
        formRow.appendChild(columnSex);
        formRow.appendChild(columnNote);

        columnName.appendChild(inputContainerName);
        columnSurname.appendChild(inputContainerSurname);
        columnPatronymic.appendChild(inputContainerPatronymic);
        columnBirthday.appendChild(inputContainerBirthday);
        columnSex.appendChild(inputContainerSex);
        columnNote.appendChild(inputContainerNote);

        inputContainerName.appendChild(inputName);
        inputContainerName.appendChild(labelName);
        inputContainerName.appendChild(tooltipName);

        inputContainerSurname.appendChild(inputSurname);
        inputContainerSurname.appendChild(labelSurname);
        inputContainerSurname.appendChild(tooltipSurname);

        inputContainerPatronymic.appendChild(inputPatronymic);
        inputContainerPatronymic.appendChild(labelPatronymic);
        inputContainerPatronymic.appendChild(tooltipPatronymic);

        inputContainerBirthday.appendChild(inputBirthday);
        inputContainerBirthday.appendChild(labelBirthday);
        inputContainerBirthday.appendChild(tooltipBirthday);

        inputContainerSex.appendChild(inputSex);
        inputContainerSex.appendChild(tooltipSex);

        inputContainerNote.appendChild(inputNote);
        inputContainerNote.appendChild(tooltipNote);

        this.cardContainer.appendChild(wrapper);

        $(`#person-${index}-birthday`).daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'DD.MM.YYYY'
            },
        });

        $('#booking-form').yiiActiveForm('add', {
            id: `person-${index}-name`,
            name: 'name',
            container: `.field-person-${index}-name`,
            input: `#person-${index}-name`,
            error: '.invalid-tooltip',
            validate: function(attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {
                    'message': 'Необходимо заполнить «Имя».'
                });
                yii.validation.string(value, messages, {
                    'message': 'Значение «Имя» должно быть строкой.',
                    'max': 50,
                    'tooLong': 'Значение «Имя» должно содержать максимум 50 символов.',
                    'skipOnEmpty': 1
                });
            },
        });

        $('#booking-form').yiiActiveForm('add', {
            id: `person-${index}-surname`,
            name: 'surname',
            container: `.field-person-${index}-surname`,
            input: `#person-${index}-surname`,
            error: '.invalid-tooltip',
            validate: function(attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {
                    'message': 'Необходимо заполнить «Фамилия».'
                });
                yii.validation.string(value, messages, {
                    'message': 'Значение «Фамилия» должно быть строкой.',
                    'max': 50,
                    'tooLong': 'Значение «Фамилия» должно содержать максимум 50 символов.',
                    'skipOnEmpty': 1
                });
            },
        });

        $('#booking-form').yiiActiveForm('add', {
            id: `person-${index}-patronymic`,
            name: 'patronymic',
            container: `.field-person-${index}-patronymic`,
            input: `#person-${index}-patronymic`,
            error: '.invalid-tooltip',
            validate: function(attribute, value, messages, deferred, $form) {
                yii.validation.string(value, messages, {
                    'message': 'Значение «Отчество» должно быть строкой.',
                    'max': 50,
                    'tooLong': 'Значение «Отчество» должно содержать максимум 50 символов.',
                    'skipOnEmpty': 1
                });
            },
        });

        $('#booking-form').yiiActiveForm('add', {
            id: `person-${index}-sex`,
            name: 'sex',
            container: `.field-person-${index}-sex`,
            input: `#person-${index}-sex`,
            error: '.invalid-tooltip',
            validate: function(attribute, value, messages, deferred, $form) {
                yii.validation.range(value, messages, {
                    'range': ['0', '1'],
                    'not': false,
                    'message': 'Значение «Пол» неверно.',
                    'skipOnEmpty': 1
                });
            },
        });

        $('#booking-form').yiiActiveForm('add', {
            id: `person-${index}-note`,
            name: 'note',
            container: `.field-person-${index}-note`,
            input: `#person-${index}-note`,
            error: '.invalid-tooltip',
            validate: function(attribute, value, messages, deferred, $form) {
                yii.validation.string(value, messages, {
                    'message': 'Значение «Примечание» должно быть строкой.',
                    'max': 50,
                    'tooLong': 'Значение «Примечание» должно содержать максимум 500 символов.',
                    'skipOnEmpty': 1
                });
            },
        });
    }

    /**
     * Description
     */
    removeToursit(index) {
        const wrapper = document.getElementById(this.getTouristId(index));

        if (wrapper) {
            wrapper.parentNode.removeChild(wrapper);
        }

        $('#booking-form').yiiActiveForm('remove', `person-${index}-name`);
        $('#booking-form').yiiActiveForm('remove', `person-${index}-surname`);
        $('#booking-form').yiiActiveForm('remove', `person-${index}-patronymic`);
        $('#booking-form').yiiActiveForm('remove', `person-${index}-sex`);
        $('#booking-form').yiiActiveForm('remove', `person-${index}-note`);
    }

    /**
     * Description
     */
    updatePrice() {
        const $form = $('#booking-form');

        const data = $form.serialize();

        $.ajax({
            url: $form.data('calcAction'),
            type: 'POST',
            data: data,
            success: (response) => {
                this.priceDisplay.textContent = response;
            }
        });
    }

    /**
     * Description
     */
    getTouristIndex() {
        return this.touristCounter++;
    }

    /**
     * Description
     */
    getTouristId(index) {
        return 'card-tourist-' + index;
    }
}