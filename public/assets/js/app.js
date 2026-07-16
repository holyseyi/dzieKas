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

  const searchInput = document.querySelector('[data-search-input]');
  const searchResults = document.querySelector('[data-search-results]');
  const searchForm = document.querySelector('[data-search-input]')?.closest('form');

  if (searchInput && searchResults && searchForm) {
    let debounce;
    searchInput.addEventListener('input', () => {
      clearTimeout(debounce);
      const query = searchInput.value.trim();
      if (query.length < 2) {
        searchResults.innerHTML = '';
        searchResults.classList.remove('is-open');
        return;
      }
      debounce = setTimeout(() => {
        fetch('/api/search?q=' + encodeURIComponent(query) + '&limit=8')
          .then(res => res.json())
          .then(data => {
            if (!data.success || !data.data.length) {
              searchResults.innerHTML = '<a class="search-box__empty">No results</a>';
              searchResults.classList.add('is-open');
              return;
            }
            searchResults.innerHTML = data.data.map(item => {
              const poster = item.poster || '/assets/images/placeholder-poster.svg';
              return '<a href="' + item.url + '">' +
                '<img src="' + poster + '" alt="" loading="lazy">' +
                '<span>' + item.title + '</span>' +
                '<small>' + item.type + (item.year ? ' • ' + item.year : '') + '</small>' +
              '</a>';
            }).join('');
            searchResults.classList.add('is-open');
          })
          .catch(() => {
            searchResults.innerHTML = '';
            searchResults.classList.remove('is-open');
          });
      }, 250);
    });

    searchForm.addEventListener('submit', () => {
      searchResults.innerHTML = '';
      searchResults.classList.remove('is-open');
    });

    document.addEventListener('click', (e) => {
      if (!searchForm.contains(e.target)) {
        searchResults.innerHTML = '';
        searchResults.classList.remove('is-open');
      }
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
