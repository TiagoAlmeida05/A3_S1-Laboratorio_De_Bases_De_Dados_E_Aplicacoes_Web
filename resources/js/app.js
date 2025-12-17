import './bootstrap';
import './notifications';
import './notifications-page';
import Echo from "laravel-echo";
import Pusher from "pusher-js";


const key = import.meta.env.VITE_PUSHER_APP_KEY;

if (!key) {
    console.error("❌ CRITICAL ERROR: VITE_PUSHER_APP_KEY is missing! Check your .env file.");
}

window.Echo = new Echo({
    broadcaster: "pusher",
    key: key,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

const userIdElement = document.querySelector("meta[name='user-id']");
const userId = userIdElement ? userIdElement.content: null;

window.closeNotification = function() {
    const card = document.getElementById('notification-card')
    if(card) card.classList.remove('show');
}

if(userId){

    window.Echo.private(`notifications.${userId}`)
        .listen('.PlatformAlert', (e) => {
            
            const badge = document.getElementById('notificationBadge');
            if(badge) badge.style.display = 'block';

            const card= document.getElementById('notification-card');
            const title= document.getElementById('notification-title');
            const message= document.getElementById('notification-message');
            const dismissBtn = card.querySelector("button");

            if(card && message && dismissBtn){
                title.innerText = "New System Alert";
                message.innerText = e.message;

                const newBtn = dismissBtn.cloneNode(true);
                dismissBtn.parentNode.replaceChild(newBtn, dismissBtn);
                
                newBtn.onclick = function(){
                    card.classList.remove('show');

                    if(e.notificationId){
                        console.log("Marking notification as read:", e.notificationId);
                        axios.post(`/notifications/${e.notificationId}/mark-as-read`);
                    }
                };
                
                card.classList.remove('show');
                void card.offsetWidth;
                card.classList.add('show');

                setTimeout(() => card.classList.remove('show'), 6000);
            }
        });
}else{
    console.log("User not authenticated. Real-time listener disabled.")
}