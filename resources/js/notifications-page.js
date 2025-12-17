function updateHeaderBadge(){
    const badge = document.getElementById('notificationBadge');
    if(!badge) return;

    const unreadOnPage = document.querySelectorAll(".unread-item").length;

    if(unreadOnPage === 0){
        badge.style.setProperty('display', 'none', 'important');
    }
}

window.markSingleAsRead = function(id, btn){
    const card = document.getElementById(`notif-${id}`);
    const text = card.querySelector('.notif-content');

    card.classList.remove('bg-white', 'border-l-4', 'border-purple-600', 'shadow-md', 'unread-item');
    card.classList.add('bg-gray-50', 'border', 'border-gray-200');

    text.classList.remove('font-bold', 'text-gray-900');
    text.classList.add('font-normal', 'text-gray-500');

    const checkmark = document.createElement('span');
    checkmark.innerHTML = '✓';
    checkmark.className = 'text-gray-300 text-xl';
    btn.parentNode.replaceChild(checkmark, btn);
    
    updateHeaderBadge();
    axios.post(`/notifications/${id}/mark-as-read`)
        .catch(err => console.error(err));
}

window.markAllOnPageRead = function() {

    const badge = document.getElementById('notificationBadge');
    if(badge) badge.style.setProperty('display', 'none', 'important');

    document.querySelectorAll('.unread-item').forEach(card => {
        const btn = card.querySelector('button');
        if(btn) {
            const text = card.querySelector('.notif-content');

            card.classList.remove('bg-white', 'border-l-4', 'border-purple-600', 'shadow-md', 'unread-item');
            card.classList.add('bg-gray-50', 'border', 'border-gray-200');

            text.classList.remove('font-bold', 'text-gray-900');
            text.classList.add('font-normal', 'text-gray-500');

            const checkmark = document.createElement('span');
            checkmark.innerHTML = '✓';
            checkmark.className = 'text-gray-300 text-xl';
            btn.parentNode.replaceChild(checkmark, btn);
        }
    });

    axios.post('/notifications/mark-all-read');
}