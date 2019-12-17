<template>
    <div>
        <comment-list-item
            v-for="comment in comments"
            :comment="comment"
            :key="comment.id"
            class="mb-3"
        ></comment-list-item>
    </div>
</template>

<script>
    import CommentListItem from './CommentListItem';

    export default {
        components: { CommentListItem },
        props: {
            comments: {
                type: Array,
                required: true
            },
            statusId: {
                type: Number,
                required: true
            }
        },
        mounted() {
            Echo.channel(`statuses.${this.statusId}.comments`)
                .listen('CommentCreated', ({comment}) => {
                    this.comments.push(comment);
                });

            EventBus.$on(`statuses.${this.statusId}.comments`, comment => {
                this.comments.push(comment);
            });
        }
    }
</script>
