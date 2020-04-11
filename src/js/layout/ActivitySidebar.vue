<template>
    <div class="quickview" :class="{'is-active': active}">
        <header class="quickview-header">
            <p class="title is-uppercase has-text-weight-semibold">Activities</p>
            <span title="Close" class="delete" @click="hideActivities()"></span>
        </header>

        <div class="quickview-body">
            <div class="quickview-block">
                <activity
                        ref="activityList"
                        v-if="active"
                        :page="activitiesPage"
                        :total.sync="totalActivities"
                        :count.sync="activitiesCount"  />
            </div>
        </div>

        <div class="quickview-footer">
            <div class="quickview-block">
                <div class="level">
                    <div class="level-left">
                        <div class="level is-mobile">
                            <div class="level-item has-text-weight-semibold has-text-info">{{ activitiesCount }}</div>
                            <div class="level-item">of</div>
                            <div class="level-item has-text-weight-semibold has-text-info">{{ totalActivities }}</div>
                        </div>
                    </div>
                    <div class="level-right has-text-right">
                        <div class="level-item">
                            <button type="button" class="button is-info is-outlined"
                                    @click="loadMoreActivities()">Load More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Activity from "../components/activities/Activity"

    export default {
        name: "ActivitySidebar",

        components: { Activity },

        props: {
            active: {
                type: Boolean,
                default: false
            }
        },

        data() {
            return {
                totalActivities: 0,
                activitiesCount: 0,
                activitiesPage: 1,
                opened: false,
            }
        },

        watch: {
            active() {
                this.opened = true;
                this.activitiesPage = 1;
                this.activitiesCount = 0;
            }
        },

        methods: {
            hideActivities() {
                this.$emit('update:active', false);
            },

            loadMoreActivities() {
                this.activitiesPage++;
            },
        }
    }
</script>