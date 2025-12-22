import 'bootstrap';
import './notifications';
import './notifications-page';
import Echo from "laravel-echo";
import Pusher from "pusher-js";


const key = import.meta.env.VITE_PUSHER_APP_KEY;

if (!key) {
    console.error("VITE_PUSHER_APP_KEY missing.");
}

window.Echo = new Echo({
    broadcaster: "pusher",
    key: key,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

const userIdElement = document.querySelector("meta[name='user-id']");
const userId = userIdElement ? userIdElement.content: null;

if(userId){

    window.Echo.private(`notifications.${userId}`)
        .listen('.PlatformAlert', (e) => {

            if(e.typeId == 2){
                console.log("New Message Received!");
                const msgBadge = document.getElementById('message-badge');

                if(msgBadge){
                    msgBadge.style.display = 'flex';

                    let currentCount = parseInt(msgBadge.innerText);
                    if(isNaN(currentCount)) currentCount = 0;
                    msgBadge.innerText = currentCount + 1;
                }
            }
            else{           
                const badge = document.getElementById('notification-badge');
                if(badge) {
                    badge.style.display = 'block';

                    let currentCount = parseInt(badge.innerText);
                    if(isNaN(currentCount)) currentCount = 0;
                    badge.innerText = currentCount + 1;
                }
            }

            const card= document.getElementById('notification-card');
            const title= document.getElementById('notification-title');
            const message= document.getElementById('notification-message');
            const dismissBtn = card.querySelector("button");

            if(card && message && dismissBtn){
                if(e.typeId == 2){
                    title.innerText = "New Message";
                }else{
                    title.innerText = "New Notification";
                }                
                message.innerText = e.message;

                const newBtn = dismissBtn.cloneNode(true);
                dismissBtn.parentNode.replaceChild(newBtn, dismissBtn);
                
                newBtn.onclick = function(){
                    card.classList.remove('show');

                    if(e.typeId != 2){
                        const badge = document.getElementById('notification-badge');
                        if(badge) {
                            let currentCount = parseInt(badge.innerText);
                            if(isNaN(currentCount)) currentCount = 1;
                            let newCount = currentCount - 1;
                            if(newCount <= 0){
                                badge.style.display = 'none';
                                badge.innerText = '';
                            }else{
                                badge.innerText = newCount;
                            }
                        }
                    }
                    
                    if(e.notificationId && e.typeId != 2){
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
}