document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const minSalaryInput = document.getElementById('minSalaryInput');
    const searchForm = document.getElementById('searchForm');

    let searchTimeout;

    function performSearch() {
        const searchTerm = searchInput.value.trim();
        const minSalary = minSalaryInput.value.trim();

        const url = new URL(window.location.href);

        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }

        if(minSalary && parseInt(minSalary) > 0){
            url.searchParams.set('min_salary', minSalary);
        }else{
            url.searchParams.delete('min_salary');
        }

        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newJobPostings = doc.querySelector('#jobPostingsContainer');
                if (newJobPostings) {
                    document.getElementById('jobPostingsContainer').innerHTML = newJobPostings.innerHTML;
                }

                const newCompanies = doc.querySelector('#companiesContainer');
                if (newCompanies) {
                    document.getElementById('companiesContainer').innerHTML = newCompanies.innerHTML;
                } else {
                    document.getElementById('companiesContainer').innerHTML = '';
                }

                const newJobSeekers = doc.querySelector('#jobSeekerContainer');
                if (newJobSeekers) {
                    document.getElementById('jobSeekerContainer').innerHTML = newJobSeekers.innerHTML;
                } else {
                    document.getElementById('jobSeekerContainer').innerHTML = '';
                }

                window.history.pushState({}, '', url.toString());
            })
            .catch(console.error);
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => performSearch(this.value), 500);
    });
    
    if(minSalaryInput){
        minSalaryInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => performSearch(this.value), 500);
    });
    }

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        clearTimeout(searchTimeout);
        performSearch();
    });
});