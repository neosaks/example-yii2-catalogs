'use strict';

/**
 * @constructor
 * @author Maxim Chichkanov
 */
function Catalogs_Widgets_Images_CatalogImages(container) {
    /**
     * Customizable constants
     */
    const LIST_INPUT = '[data-widget="catalog-file-list"]';
    const DELETE_ACTION = '[data-widget="catalog-file-delete"]';

    /**
     * Private properties
     */
    var $container = $(container);
    var $list = $container.find(LIST_INPUT);

    /**
     * Run
     */
    run();

    /**
     * Description.
     * @return {void}
     */
    function run() {
        $(document).on('click', DELETE_ACTION, function () {
            const $self = $(this);
            if ($self.hasClass('fa-times')) {
                $self
                    .removeClass('fa-times')
                    .addClass('fa-undo');

                deleteImage($self.data('key').toString());
            } else if ($self.hasClass('fa-undo')) {
                $self
                    .removeClass('fa-undo')
                    .addClass('fa-times');

                restoreImage($self.data('key').toString());
            }
        });

        $(document).on('pjax:complete', function () {
            const list = $list.val().split(',');
            $container.find('img').each(function (index, image) {
                const $image = $(image);

                if (!list.includes($image.data('key').toString())) {
                    $image
                        .closest('figure')
                        .find(DELETE_ACTION)
                        .removeClass('fa-times')
                        .addClass('fa-undo');

                    $image
                        .css('opacity', '.2');
                }
            });
        });
    }

    /**
     * Description.
     * @param {stirng} key
     * @return {void}
     */
    function deleteImage(key) {
        /** @type {array} list */
        const list = $list.val().split(',');
        list.splice(list.indexOf(key), 1);
        $list.val(list.join(','));

        $container.find('img[data-key="' + key + '"]')
            .css('opacity', '.2');
    }

    /**
     * Description.
     * @param {stirng} key
     * @return {void}
     */
    function restoreImage(key) {
        /** @type {array} list */
        const list = $list.val().split(',');
        list.push(key);
        $list.val(list.join(','));

        $container.find('img[data-key="' + key + '"]')
            .css('opacity', '');
    }
};
