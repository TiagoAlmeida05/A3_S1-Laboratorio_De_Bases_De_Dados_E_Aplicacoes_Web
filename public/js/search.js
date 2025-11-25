document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const jobPostingsContainer = document.getElementById('jobPostingsContainer');
    const resultsCount = document.getElementById('resultsCount');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const clearSearch = document.getElementById('clearSearch');
    
    let searchTimeout;
    
    function performSearch(searchTerm) {

        const url = new URL(window.location.href);
        if (searchTerm.trim()) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newJobPostings = doc.querySelector('#jobPostingsContainer');
            if (newJobPostings) {
                jobPostingsContainer.innerHTML = newJobPostings.innerHTML;
            }
            
            const newResultsCount = doc.querySelector('#resultsCount');
            if (newResultsCount) {
                resultsCount.innerHTML = newResultsCount.innerHTML;
            }
            
            window.history.pushState({}, '', url.toString());
            
            updateClearButton(searchTerm);
            
            loadingSpinner.style.display = 'none';
        })
        .catch(error => {
            console.error('Search error:', error);
            loadingSpinner.style.display = 'none';
        });
    }
    
    function updateClearButton(searchTerm) {
        if (clearSearch) {
            if (searchTerm.trim()) {
                clearSearch.style.display = 'inline';
            } else {
                clearSearch.style.display = 'none';
            }
        }
    }
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value;
        
        searchTimeout = setTimeout(function() {
            performSearch(searchTerm);
        }, 500);
    });
    
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        clearTimeout(searchTimeout);
        performSearch(searchInput.value);
    });

});