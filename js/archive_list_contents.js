/**
 * @file
 * Archive List Contents behaviors.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Attaches readmore to archive list.
   */
  Drupal.behaviors.archiveListContents = {
    attach: function (context, settings) {
      once('archivelist-once', jQuery('.archivelist-readmore')).forEach(archive => {
        jQuery(archive).readmore({
          moreLink: '<a href="#">...</a>',
          lessLink: '<a href="#">Close</a>',
          blockCSS: 'display: none; width: 100%;'
        });
      });
    }
  };

} (jQuery, Drupal));
