<template>
    <div class="card border-0 mb-3 shadow-sm">
        <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center mb-3">
                <img class="rounded mr-3 shadow-sm m-sm-2" height="40px" width="40px" :src="status.user.avatar" :alt="status.user.name">
                <div  class="">
                    <h5 class="mb-1"><a class="text-decoration-none" :href="status.user.link" v-text="status.user.name"></a></h5>
                    <div class="small text-muted" v-text="status.ago"></div>
                </div>
            </div>
            <p class="card-text text-secondary" v-text="status.body"></p>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">

            <like-btn
                dusk="like-btn"
                :url="`/statuses/${status.id}/likes`"
                :model="status"
            ></like-btn>

            <div class="text-secondary">
                <font-awesome-icon icon="fa-regular fa-thumbs-up" />
                <span dusk="likes-count">{{ status.likes_count }}</span>
            </div>
        </div>

        <div class="card-footer">

            <div v-for="comment in comments" class="mb-2">

                <div class="d-flex">
                    <img class="rounded shadow-sm me-2" height="40px" width="35px" :src="comment.user.avatar" :alt="comment.user.name">
                    <div class="flex-grow-1">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-2 text-secondary">
                                <a class="text-decoration-none" :href="comment.user.link"><strong>{{ comment.user.name }}</strong></a>
                                {{ comment.body }}
                            </div>
                        </div>
                        <small class="float-end badge bg-primary rounded-pill py-1 px-2 mt-1" dusk="comment-likes-count">
                            <font-awesome-icon icon="fa-solid fa-thumbs-up" />
                            {{ comment.likes_count }}
                        </small>
                        <like-btn
                            dusk="comment-like-btn"
                            :url="`/comments/${comment.id}/likes`"
                            :model="comment"
                            class="comments-like-btn"
                        ></like-btn>
                    </div>
                </div>
            </div>

            <form @submit.prevent="addComment" v-if="isAuthenticated"> <!-- prevent -> previene que se recargue la página-->
                <div class="d-flex align-items-center" v-if="currentUser">
                    <img class="rounded shadow-sm me-2" width="45px"
                         :src="currentUser.avatar"
                         :alt="currentUser.name">
                    <div class="input-group">
                        <textarea v-model="newComment"
                                  class="form-control border-0 shadow-sm"
                                  name="comment"
                                  placeholder="Escribe un comentario..."
                                  rows="1"
                                  required
                        ></textarea>
                        <button class="btn btn-primary" dusk="comment-btn">Enviar</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</template>

<script>
    import LikeBtn from "./LikeBtn";
    export default {
        components: { LikeBtn },
        props: { // validar las propiedades del objeto
            status: {
                type: Object,
                required: true
            },
        },
        data(){
            return {
                newComment: '',
                comments: this.status.comments,
            }
        },
        methods: {
            addComment(){
                axios.post(`/statuses/${this.status.id}/comments`, {body: this.newComment})
                    .then(res => {
                        this.newComment = '';
                        this.comments.push(res.data.data);
                    })
                    .catch(err => {
                        console.log(err.response.data)
                    })
            }
        }
    }
</script>

<style scoped>

</style>
