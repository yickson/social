<template>
    <div class="d-flex justify-content-between bg-light p-3 rounded mb-3 shadow-sm">
        <div>
            <div v-if="localFriendshipStatus === 'pending'">
                <span v-text="sender.name"></span> te ha enviado una solicitud de amistad
            </div>
            <div v-if="localFriendshipStatus === 'accepted'">
                Tú y <span v-text="sender.name"></span> son amigos
            </div>
            <div v-if="localFriendshipStatus === 'denied'">
                Solicitud denegada de <span v-text="sender.name"></span>
            </div>
            <div v-if="localFriendshipStatus === 'deleted'">
                Solicitud eliminada de <span v-text="sender.name"></span>
            </div>
        </div>
        <div>
            <button class="btn btn-sm btn-primary" v-if="localFriendshipStatus === 'pending'" dusk="accept-friendship" @click="acceptFriendshipRequest">Aceptar solicitud</button>
            <button class="btn btn-sm btn-warning" v-if="localFriendshipStatus === 'pending'" dusk="deny-friendship" @click="denyFriendshipRequest">Denegar solicitud</button>
            <button class="btn btn-sm btn-danger" v-if="localFriendshipStatus !== 'deleted'" dusk="delete-friendship" @click="deleteFriendship">Eliminar</button>
        </div>
    </div>
</template>

<script>
    export default {
        props:{
            sender: {
                type: Object,
                required: true
            },
            friendshipStatus: {
                type: String,
                required: true
            }
        },
        data(){
            return {
                localFriendshipStatus: this.friendshipStatus
            }
        },
        methods:{
            acceptFriendshipRequest(){
                axios.post(`/accept-friendships/${this.sender.name}`)
                    .then(res => {
                        this.localFriendshipStatus = res.data.friendship_status
                    })
                    .catch(err => {
                        console.log(err.response.data);
                    })
            },
            denyFriendshipRequest(){
                axios.delete(`/accept-friendships/${this.sender.name}`)
                    .then(res => {
                        this.localFriendshipStatus = res.data.friendship_status
                    })
                    .catch(err => {
                        console.log(err.response.data);
                    })
            },
            deleteFriendship(){
                axios.delete(`/friendships/${this.sender.name}`)
                    .then(res => {
                        this.localFriendshipStatus = res.data.friendship_status
                    })
                    .catch(err => {
                        console.log(err.response.data);
                    })
            }
        }
    }
</script>

<style scoped>

</style>