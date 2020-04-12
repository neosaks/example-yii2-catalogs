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
class AttributesWidget {
    /**
     * @type {AttributesWidget_Table} Description.
     */
    table = new AttributesWidget_Table('.attributes-widget .attributes-table table');

    /**
     * @type {AttributesWidget_List} Description.
     */
    list = new AttributesWidget_List('.attributes-widget .attributes-list');

    /**
     * @type {AttributesWidget_Modal} Description.
     */
    modal;

    /**
     * Description.
     */
    constructor(_modalSelector) {
        this.modal = new AttributesWidget_Modal(_modalSelector);
        this.modal.addEventListener('beforeSubmit', e => {
            this.list.add(e.attribute);
            this.modal.hide();
            return false;
        });

        this.table.addEventListener('update', e => {
            let attribute = this.list.get(e.key);
            if (attribute) {
                this.modal.load(attribute);
                this.modal.show();
            }
        });

        this.table.addEventListener('delete', e => {
            if (confirm('Are you sure you want to delete this item?')) {
                this.list.delete(e.key);
            }
        });
    }

    /**
     * Description.
     *
     * @return {void}
     */
    syncData() {
        this.table.renderAttributes(this.list.getAll());
    }
}

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class AttributesWidget_Base {
    /**
     * @type {object} Description.
     */
    listeners = {};

    /**
     * Description.
     *
     * @param {string} type
     * @param {function} listener
     * @return {void}
     */
    addEventListener(type, listener) {
        (type in this.listeners)
            ? this.listeners[type].push(listener)
            : this.listeners[type] = [listener];
    }

    /**
     * Description.
     *
     * @param {string} type
     * @param {array} $options
     * @return {void}
     */
    trigger(type, options) {
        let proceed = true;

        if (type in this.listeners) {
            for (let listener of this.listeners[type]) {
                if (listener.call(undefined, options) === false) {
                    proceed = false;
                }
            }
        }

        return proceed;
    }
}

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class AttributesWidget_Modal extends AttributesWidget_Base {
    /**
     * @type {JQuery<HTMLElement>} Description.
     */
    $modal;

    /**
     * @type {JQuery<HTMLFormElement>} Description.
     */
    $form;

    /**
     * Description.
     *
     * @param {string} _modalSelector
     */
    constructor(_modalSelector) {
        super();

        this.$modal = $(_modalSelector);
        this.$form = this.$modal.closest('form');

        this.$modal.on('hidden.bs.modal', e => {
            this.reset();

            return this.trigger('afterHide', {});
        });

        this.$form.on('beforeSubmit', e => {
            let attribute = new AttributesWidget_Attribute(
                $(e.target).serializeArray()
            );

            return this.trigger('beforeSubmit', {attribute});
        });
    }

    /**
     * Description.
     *
     * @return {void}
     */
    show() {
        this.$modal.modal('show');
    }

    /**
     * Description.
     *
     * @return {void}
     */
    hide() {
        this.$modal.modal('hide');
    }

    /**
     * Description.
     *
     * @param {AttributesWidget_Attribute} attribute
     * @return {void}
     */
    load(attribute) {
        this.$form.find(`#attribute-type option[value="${attribute.type}"]`)
            .prop('selected', 'selected');

        this.$form.find('#attribute-name').val(attribute.name);
        this.$form.find('#attribute-hint').val(attribute.hint);
        this.$form.find('#attribute-placeholder').val(attribute.placeholder);
        this.$form.find('#attribute-description').val(attribute.description);

        this.$form.find(`#attribute-status option[value="${attribute.status}"]`)
            .prop('selected', 'selected');
    }

    /**
     * Description.
     *
     * @return {void}
     */
    reset() {
        this.$form.trigger('reset');
    }
}

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class AttributesWidget_Table extends AttributesWidget_Base {
    /**
     * @type {JQuery<HTMLTableElement>} Description.
     */
    $table;

    /**
     * Description.
     *
     * @param {string} _tableSelector
     */
    constructor(_tableSelector) {
        super();

        this.$table = $(_tableSelector);

        this.$table.on('click', '[data-action="update"]', e => {
            this.trigger('update', {key: $(e.currentTarget).data('key')});
        });

        this.$table.on('click', '[data-action="delete"]', e => {
            this.trigger('delete', {key: $(e.currentTarget).data('key')});
        });
    }

    /**
     * Description.
     *
     * @param {AttributesWidget_Attribute[]} attributes
     * @return {void}
     */
    renderAttributes(attributes) {}
}

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class AttributesWidget_List extends AttributesWidget_Base {
    /**
     * @type {JQuery<HTMLElement>} Description.
     */
    $list;

    /**
     * @type {AttributesWidget_Attribute} Description.
     */
    // list = {};

    /**
     * Description.
     */
    created = [];

    /**
     * Description.
     */
    updated = {};

    /**
     * Description.
     */
    deleted = {};

    /**
     * Description.
     *
     * @param {string} _listSelector
     */
    constructor(_listSelector) {
        super();

        this.$list = $(_listSelector);

        // this.$list.find('.attributes-fieldset').find('input').each(input => {
        //     console.log('input', input);
        // });

        this.$list.find('.attributes-fieldset').each((index, fieldset) => {
            let $fieldset = $(fieldset);

            let attribute = new AttributesWidget_Attribute(
                $fieldset.find('input').serializeArray(),
                $fieldset.data('key')
            );

            this.add(attribute);
        });
    }

    /**
     * Description.
     *
     * @return {object}
     */
    getAll() {
        return this.list;
    }

    /**
     * Description.
     *
     * @param {string|integer} key
     * @return {AttributesWidget_Attribute|undefined}
     */
    get(key) {
        if (this.list[key]) {
            return this.list[key];
        }
    }

    /**
     * Description.
     *
     * @param {string|key} key
     * @return {void}
     */
    delete(key) {
        // this.$list.remove(`.attributes-fieldset[data-key="${key}"]`);

        if (this.list[key]) {
            delete this.list[key];
        }
    }

    /**
     * Description.
     *
     * @param {AttributesWidget_Attribute} attribute
     * @return {void}
     */
    add(attribute) {
        this.created.push(attribute);
        // if (attribute.id !== undefined) {
            // this.list[attribute.id] = attribute;
            // добавить в $list
        // }
    }
}

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class AttributesWidget_Attribute {
    /**
     * @type {array} Description.
     */
    properties = [];

    /**
     * @type {string|number} Description.
     */
    key;

    /**
     * Description.
     *
     * @param {array} _properties
     */
    constructor(_properties, key) {
        this.properties = _properties;
        this.key = key;
    }

    /**
     * Description.
     *
     * @param {string} name
     * @param {any} defaultValue
     * @return {any}
     */
    getProperty(name, defaultValue = undefined) {
        let propertyName = this.key
            ? `Attribute[${this.key}][${name}]`
            : `Attribute[${name}]`;

        let property = this.properties
            .find(property => property.name === propertyName);

        if (property !== undefined) {
            return property.value;
        }

        return defaultValue;
    }

    /**
     * Description.
     */
    get id() {
        return this.getProperty('id');
    }

    /**
     * Description.
     */
    get type() {
        return this.getProperty('type');
    }

    /**
     * Description.
     */
    get name() {
        return this.getProperty('name');
    }

    /**
     * Description.
     */
    get hint() {
        return this.getProperty('hint');
    }

    /**
     * Description.
     */
    get placeholder() {
        return this.getProperty('placeholder');
    }

    /**
     * Description.
     */
    get description() {
        return this.getProperty('description');
    }

    /**
     * Description.
     */
    get status() {
        return this.getProperty('status');
    }
}
