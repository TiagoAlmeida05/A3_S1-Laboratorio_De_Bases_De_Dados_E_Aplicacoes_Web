document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    
    if (!searchForm) return;

    let searchTimeout;

    function performSearch() {
        const url = new URL(window.location.href);
        
        const formData = new FormData(searchForm);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                params.append(key, value);
            }
        }

        url.search = params.toString();

        fetch(url.toString(), { 
            headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const containers = [
                'jobPostingsContainer', 
                'companiesContainer', 
                'jobSeekerContainer'
            ];

            containers.forEach(id => {
                const newContent = doc.getElementById(id);
                const currentContainer = document.getElementById(id);

                if (currentContainer) {
                    if (newContent) {
                        currentContainer.innerHTML = newContent.innerHTML;
                    } else {
                        currentContainer.innerHTML = '';
                    }
                }
            });

            window.history.pushState({}, '', url.toString());
        })
        .catch(console.error);
    }


    const textInputs = searchForm.querySelectorAll('input[type="text"], input[type="number"]');
    textInputs.forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
    });

    const checkboxes = searchForm.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(box => {
        box.addEventListener('change', () => {
            performSearch(); 
        });
    });

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        clearTimeout(searchTimeout);
        performSearch();
    });
});