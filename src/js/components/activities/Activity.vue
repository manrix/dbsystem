<template>
    <div>
        <b-loading
                :is-full-page="false"
                :active.sync="loading">
        </b-loading>
        <div class="activities-container" v-if="hasActivity">
            <div class="content">
                <div class="timeline">
                    <template v-for="activity in activities">
                        <activity-item :activity="activity" :user="user" />
                    </template>
                </div>
            </div>
        </div>
        <div v-else class="section">
            <no-results message="No activity found">
                <template slot="icon">
                    <i class="far fa-list-alt fa-5x"></i>
                </template>
            </no-results>
        </div>
    </div>
</template>

<script>
    import ActivityItem from './ActivityItem'

    export default {
        name: "Activity",

        props: {
            user: {
                type: String,
                default: null
            },
            total: {
                type: Number,
                default: 0
            },
            count: {
                type: Number,
                default: 0
            },
            perPage: {
                type: Number,
                default: 10
            },
            page: {
                type: Number,
                default: 1
            },
        },

        components: {ActivityItem},

        data() {
            return {
                activities: [],
                loading: false,
            }
        },

        watch: {
            page() {
                this.getData();
            }
        },

        computed: {
            hasActivity() {
                return !!this.activities.length;
            }
        },

        methods: {
            getData() {
                const params = Object.assign({}, {
                    page: this.page,
                    perPage: this.perPage,
                });

                if (this.user) {
                    params.user = this.user;
                }

                this.loading = true;
                this.axios.get('activities', {params: params})
                    .then(response => {
                        this.activities.push(...response.data.data);
                        this.loading = false;
                        this.$emit('update:total', response.data.total);
                        this.$emit('update:count', this.activities.length);
                    })
                    .catch((error) => {
                        this.loading = false;
                    })
            },
        },

        mounted() {
            this.getData();
        }
    }
</script>