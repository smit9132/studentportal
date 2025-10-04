// js/validation.js - minimal client-side validation helpers
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('form[data-validate="true"]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var valid = true;
      form.querySelectorAll('[required]').forEach(function (el) {
        if (!el.value.trim()) {
          valid = false;
          el.classList.add('is-invalid');
        } else {
          el.classList.remove('is-invalid');
        }
      });
      if (!valid) {
        e.preventDefault();
        var alertBox = form.querySelector('.form-error');
        if (alertBox) alertBox.textContent = 'Please fill the required fields.';
      }
    });
  });
});
