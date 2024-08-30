function sticky_relocate() {
  var window_top = jQuery(window).scrollTop();
  var div_top;
  if (jQuery(".x-main").offset()) {
    div_top = jQuery(".x-main").offset().top;
  }
  if (window_top > div_top) {
    jQuery(".x-sidebar").addClass("stick");
  } else {
    jQuery(".x-sidebar").removeClass("stick");
  }
}
(function ($) {
  $(window).scroll(sticky_relocate);
  sticky_relocate();
})(jQuery);
