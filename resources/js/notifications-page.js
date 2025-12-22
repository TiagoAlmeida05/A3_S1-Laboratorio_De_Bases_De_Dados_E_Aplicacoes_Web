function updateHeaderBadge(){
    const badge = document.getElementById('notification-badge');
    if(!badge) return;

    const unreadOnPage = document.querySelectorAll(".unread-notif").length;

    if(unreadOnPage === 0){
        badge.style.setProperty('display', 'none', 'important');
    }
}

window.markSingleAsRead = function(id, btn){
    const card = document.getElementById(`notif-${id}`);
    const text = card.querySelector('.notif-content');

    card.classList.remove('unread-notif');
    card.classList.add('read-notif');

    text.classList.remove('fw-bold');
    text.classList.add('text-muted');

    const checkmark = document.createElement('span');
    checkmark.innerHTML = '✓';
    checkmark.className = 'text-success';
    btn.parentNode.replaceChild(checkmark, btn);
    
    updateHeaderBadge();
    axios.post(`/notifications/${id}/mark-as-read`)
        .catch(err => console.error(err));
}

window.markAllOnPageRead = function() {

    const badge = document.getElementById('notification-badge');
    if(badge) badge.style.setProperty('display', 'none', 'important');

    document.querySelectorAll('.unread-notif').forEach(card => {
        const btn = card.querySelector('button');
        if(btn) {
            const text = card.querySelector('.notif-content');

            card.classList.remove('unread-notif');
            card.classList.add('read-notif');

            text.classList.remove('fw-bold');
            text.classList.add('text-muted');

            const checkmark = document.createElement('span');
            checkmark.innerHTML = '✓';
            checkmark.className = 'text-success';
            btn.parentNode.replaceChild(checkmark, btn);
        }
    });

    axios.post('/notifications/mark-all-read');
}