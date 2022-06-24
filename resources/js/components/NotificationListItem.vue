<template>
  <div class="d-flex align-items-center"
    :class="isRead ? '' : 'bg-light'">
      <a :dusk="notification.id"
         :href="notification.data.link"
         class="dropdown-item"
      >{{ notification.data.message }}</a>
      <button v-if="isRead"
              @click.stop="markAsUnread"
              :dusk="`mark-as-unread-${notification.id}`"
              class="btn btn-link me-2"
        >
          <font-awesome-icon class="icon" icon="fa-solid fa-circle-check" />
          <span class="position-fixed bg-dark text-white ms-2 py-1 px-2 rounded">Marcar como NO leída</span>
      </button>
      <button v-else
              @click.stop="markAsRead"
              :dusk="`mark-as-read-${notification.id}`"
              class="btn btn-link me-2"
        >
          <font-awesome-icon class="icon" icon="fa-regular fa-circle-check" />
          <span class="position-fixed bg-dark text-white ms-2 py-1 px-2 rounded">Marcar como leída</span>
      </button>
  </div>
</template>

<script>
export default {
    props: {
        notification: Object
    },
    data() {
        return {
            isRead: !! this.notification.read_at  // !! convierte a boolean | null -> false, 'date' -> true
        }
    },
    methods: {
        markAsRead() {
            axios.post(`/read-notifications/${this.notification.id}`)
                .then(res => {
                    this.isRead = true;
                    EventBus.$emit('notification-read');
                })
        },
        markAsUnread() {
            axios.delete(`/read-notifications/${this.notification.id}`)
                .then(res => {
                    this.isRead = false;
                    EventBus.$emit('notification-unread');
                })
        }
    }
}
</script>

<style lang="scss" scoped>
    button > span {
        display: none;
    }
    .icon {
        &:hover {
            & + span {
                display: inline;
            }
        }
    }
</style>
