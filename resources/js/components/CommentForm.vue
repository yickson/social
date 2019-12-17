<template>
    <form @submit.prevent="addComment" v-if="isAuthenticated" class="mb-3">
        <div class="d-flex align-items-center">
            <img class="rounded shadow-sm mr-2" width="34px"
                 :src="currentUser.avatar"
                 :alt="currentUser.name">
            <div class="input-group">
                <textarea v-model="newComment"
                    class="form-control border-0"
                    name="comment"
                    placeholder="Escribe un comentario..."
                    rows="1"
                    required
                ></textarea>
                <div class="input-group-append">
                    <button class="btn btn-primary" dusk="comment-btn">Enviar</button>
                </div>
            </div>
        </div>
    </form>
    <div v-else class="text-center mb-3">
        Debes <a href="/login">autenticarte</a> para poder comentar
    </div>
</template>

<script>
    export default {
        props: {
            statusId: {
                type: Number,
                required: true
            }
        },
        data() {
            return {
                newComment: '',
            }
        },
        methods: {
            addComment(){
                axios.post(`/statuses/${this.statusId}/comments`, {body: this.newComment})
                    .then(res => {
                        this.newComment = '';
                        EventBus.$emit(`statuses.${this.statusId}.comments`, res.data.data);
                    })
                    .catch(err => {
                        console.log(err.response.data)
                    })
            }
        }
    }
</script>
