/**
 * @file
 */
'use strict';

(function ($, Drupal) {

  Drupal.behaviors.bitrix_widget_button = {
    attach: function (context, settings) {
      let widgetButton = $('.bitrix-widget-button', context);

      widgetButton.click(function () {
        let widgetScript = settings.bitrix_widget_code;

        if (!widgetScript) {
          return false;
        }

        // Append script element to page.
        $(widgetScript).insertAfter($(this));

        // Remove widget button.
        $(this).remove();

        setTimeout(function () {
          $('.b24-widget-button-icon-container').click();
        }, 500);
      })
    }
  };
})(jQuery, Drupal);
