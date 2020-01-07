/**
 * @file aavi_forms.js
 *
 * Provides jQueryUI Datepicker integration with Better Exposed Filters.
 */
jQuery(document).ready(function ($) {
  $(document).on("click", ".county-select-all", function(e) {
e.preventDefault();
console.log('clicked');
    // Select all from multiselect
    $(this).parent().siblings('select').children('option').prop('selected', true);

  });

});
