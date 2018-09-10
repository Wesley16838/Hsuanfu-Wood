$(function() {
  $.validator.addMethod('dateAfter', function (value, element, params) {
      // if start date is valid, validate it as well
      var start = $(params);
      if (!start.data('validation.running')) {
          $(element).data('validation.running', true);
          setTimeout($.proxy(

          function () {
              this.element(start);
          }, this), 0);
          setTimeout(function () {
              $(element).data('validation.running', false);
          }, 0);
      }
      return this.optional(element) || this.optional(start[0]) || new Date(value) > new Date($(params).val());

  });
});

jQuery.validator.addMethod("strings", function(value, element) {
    return this.optional(element) || /^[\u0391-\uFFE5|,\d,A-Za-z,\-,_]+$/.test(value);
}, "字串不可含特殊符號及空白");

jQuery.extend(jQuery.validator.messages, {
    dateAfter : "Must be after corresponding start date"
});
