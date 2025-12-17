import axios from 'axios';

console.log("✅ Notifications.js loaded");

document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notificationBell');
    if (bell) {     
        bell.addEventListener('click', function(e) {
            console.log("🔔 Bell clicked!"); 
            loadNotifications();
        });
    } else {
        console.error("❌ Could not find element with id 'notificationBell'");
    }
});

async function loadNotifications() {
    const list = document.getElementById('notificationList');
    const badge = document.getElementById('notificationBadge')
    
    if(badge) badge.style.display = 'none';
    axios.post('/notifications/mark-all-read').catch(err => console.error(err));

    try {
        const response = await axios.get('/notifications/fetch');
        const notifications = response.data;
        list.innerHTML = '';

        if (notifications.length === 0) {
            list.innerHTML = '<li class="dropdown-item text-muted text-center">No notifications</li>';
        } else {
            notifications.forEach(notif => {

                const isUnread = (notif.read_date === null);
                const statusClass = isUnread ? 'unread' : 'read';

                let dateStr = "Just now";
                if (notif.issue_date) {
                    dateStr = new Date(notif.issue_date).toLocaleDateString();
                }

                const item = `
                    <li onclick="markItemAsRead(${notif.id}, this)">
                        <div class="notification-item ${statusClass}">
                            <span class="notification-date">${dateStr}</span>
                            <span class="notification-content">${notif.content}</span>
                        </div>
                    </li>
                `;
                list.innerHTML += item;
            });
        }
    } catch (error) {
        console.error("❌ CRASHED:", error);
    }
}

window.markItemAsRead = function(id, element){
    const div = element.querySelector('.notification-item');
    if(div.classList.contains('unread')){
        div.classList.remove('unread');
        div.classList.add('read');

        axios.post(`/notifications/${id}/mark-as-read`)
            .catch(err => console.error("Failed to mark read", err))
    }
}

window.markAllNotificationsRead = function(e){
    if(e){
        e.preventDefault();
        e.stopPropagation();
    }

    console.log("Cleaning up notifications...");

    const badge = document.getElementById('notificationBadge');
    if(badge) badge.style.display = 'none';

    const unreadItems = document.querySelectorAll('.notification-item.unread');
    unreadItems.forEach(item => {
        item.classList.remove('unread');
        item.classList.add('read');
    });

    axios.post('/notifications/mark-all-read')
        .then(response => {
            console.log("All notifications marked as read in DB.")
        })
        .catch(error => {
            console.error("Error marking all as read:", error);
        })
}