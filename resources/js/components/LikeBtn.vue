<template>
    <button
            @click="toggle()"
            :class="getBtnClasses"
    >
        <font-awesome-icon :icon="getIconClasses" class="btn-off"/>
        {{ getText }}
    </button>
</template>

<script>
export default {
    props: {
        model: {
            type: Object,
            required: true
        },
        url: {
            type: String,
            required: true
        }
    },
    methods: {
        toggle(){
            let method = this.model.is_liked ? 'delete' : 'post';
            axios[method](this.url)
                .then(res => {
                    this.model.is_liked = ! this.model.is_liked;
                    this.model.likes_count = res.data.likes_count;
                })
        }
    },
    computed: {
        getText(){
            return this.model.is_liked ? 'TE GUSTA' : 'ME GUSTA'; // si es verdadero devuelve TE GUSTA Y SI NO ME GUSTA
        },
        getBtnClasses(){
            return [
                this.model.is_liked ? 'fw-bold' : '',
                'btn', 'btn-link', 'btn-sm',
            ]
        },
        getIconClasses(){
            return [
                this.model.is_liked ? 'fa-solid' : 'fa-regular',
                'fa-thumbs-up',
            ]
        }
    }
}
</script>

<style lang="scss" scoped>
.comments-like-btn {
    font-size: 0.7em;
    padding-left: 0;
    .btn-off {
        display: none;
    }
}

</style>
