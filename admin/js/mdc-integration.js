// Initialize MDC Web components on admin pages
window.addEventListener('DOMContentLoaded', function () {
  if (window.mdc) {
    const textFields = [].map.call(document.querySelectorAll('.mdc-text-field'), function(el) {
      return new mdc.textField.MDCTextField(el);
    });
    const switches = [].map.call(document.querySelectorAll('.mdc-switch'), function(el) {
      return new mdc.switchControl.MDCSwitch(el);
    });
    const buttons = [].map.call(document.querySelectorAll('.mdc-button'), function(el) {
      return new mdc.ripple.MDCRipple(el);
    });
    const dataTables = [].map.call(document.querySelectorAll('.mdc-data-table'), function(el) {
      return new mdc.dataTable.MDCDataTable(el);
    });
  }
});
