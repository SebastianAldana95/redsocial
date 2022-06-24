<template>
    <li class="nav-item dropdown">
        <a id="navbarDropdownNotifications"
           class="nav-link dropdown-toggle"
           :class="count ? 'text-primary font-weight-bold' : ''"
           href="#"
           dusk="notifications"
           role="button"
           data-bs-toggle="dropdown"
           aria-haspopup="true"
           aria-expanded="false"
            ><slot></slot> {{ count }}
        </a>

        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownNotifications">
            <div class="dropdown-header text-center">Notificaciones</div>
            <notification-list-item
                v-for="notification in notifications"
                :notification="notification"
                :key="notification.id"
            ></notification-list-item>

        </div>
    </li>
</template>

<script>
import NotificationListItem from './NotificationListItem';

export default {
    components: { NotificationListItem },
    data(){
        return {
            notifications: [],
            count: ''
        }
    },
    created() {  // se ejecuta antes de mounted
        axios.get('/notifications')
            .then(res => {
                this.notifications = res.data;
                this.unreadNotifications();
            })

        EventBus.$on('notification-read', () => {
            if (this.count === 1) {
                return this.count = ''
            }
            this.count--;
        })

        EventBus.$on('notification-unread', () => {
            this.count++;
        })

    },
    methods: {
        unreadNotifications(){
            this.count = this.notifications.filter(notification => {
                return notification.read_at === null;
            }).length || ''
        }
    }
}
</script>

<style scoped>

</style>
