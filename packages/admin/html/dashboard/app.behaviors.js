/* global System */

(function () {
  
  System.ui.behaviors.selectElementOnly = function (element, oldElement, styleClass) {
    if ('string' !== typeof styleClass) {
      styleClass = 'selected';
    }

    if (oldElement) {
      System.ui.utility.removeClass(oldElement, "selected");
    }

    System.ui.utility.addClass(element, "selected");
    return element;
  };

})(System);