document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      nav.classList.toggle('is-open');
    });
  }

  const adminToggle = document.querySelector('[data-admin-toggle]');
  const adminSidebar = document.querySelector('[data-admin-sidebar]');
  if (adminToggle && adminSidebar) {
    adminToggle.addEventListener('click', () => {
      adminSidebar.classList.toggle('is-open');
    });
  }

  const dropZone = document.getElementById('drop-zone');
  if (dropZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
      dropZone.addEventListener(eventName, () => dropZone.style.borderColor = 'var(--primary)', false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, () => dropZone.style.borderColor = 'var(--border)', false);
    });

    dropZone.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      const files = dt.files;
      const form = dropZone.querySelector('form');
      const input = form.querySelector('input[type="file"]');
      input.files = files;
      form.submit();
    }, false);
  }
});
