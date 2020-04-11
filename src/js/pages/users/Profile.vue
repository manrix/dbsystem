<template>
    <base-layout>
        <div class="box">
            <div class="columns is-multiline">
                <div class="column is-7-tablet">
                    <p class="subtitle is-4 has-text-centered-mobile">
                        <span class="has-text-grey">#{{ user.id }}</span> - {{ user.name }}
                    </p>
                </div>
                <div class="column is-5-tablet has-text-right has-text-centered-mobile">
                    <b-field>
                        <button type="button" class="button is-link"
                                @click.prevent="editProfile()">
                            <b-icon icon="edit" size="is-small" />
                            <span>Edit</span>
                        </button>
                    </b-field>
                </div>
                <div class="column is-12">
                    <div class="columns">
                        <div class="column is-3-desktop">
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <div class="level-item">
                                        <span class="heading is-marginless">Joined on:</span>
                                    </div>
                                    <div class="level-item">
                                        <span class="has-text-weight-semibold">{{ formatToLocalDate(user.created_at) || '---' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <div class="level-item">
                                        <span class="heading is-marginless">Space used:</span>
                                    </div>
                                    <div class="level-item">
                                        <span class="has-text-weight-semibold">{{ formatSpace(user.space_used) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="tabs is-boxed is-navigation">
                <ul>
                    <li :class="{'is-active': activeTab === 'overview'}">
                        <a @click="activeTab = 'overview'">
                            <span>Overview</span>
                        </a>
                    </li>
                    <li :class="{'is-active': activeTab === 'backups'}">
                        <a @click="activeTab = 'backups'">
                            <span>Backups</span>
                            <span class="tag is-rounded">{{ user.backups_count }}</span>
                        </a>
                    </li>
                    <li :class="{'is-active': activeTab === 'tasks'}">
                        <a @click="activeTab = 'tasks'">
                            <span>Tasks</span>
                            <span class="tag is-rounded">{{ user.tasks_count }}</span>
                        </a>
                    </li>
                    <li :class="{'is-active': activeTab === 'activities'}">
                        <a @click="activeTab = 'activities'">
                            <span>Activities</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <transition name="slide-next">
            <div class="columns is-multiline" v-show="activeTab === 'overview'">
                <div class="column is-half-desktop">
                    <div class="box" style="height: 100%">
                        <h6 class="is-uppercase title is-6 is-spaced">Tasks Report</h6>
                        <div v-show="hasTasks">
                            <div class="chart-container">
                                <canvas id="tasksChart"></canvas>
                            </div>
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-success">{{ user.active_tasks_count }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Active</small>
                                        </p>
                                    </div>
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-danger">{{ inactiveTasksTotal }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Inactive</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <no-results v-show="!hasTasks" style="padding-top: 1.5rem;">
                            <template slot="icon">
                                <i class="fa fa-chart-pie fa-5x"></i>
                            </template>
                        </no-results>
                    </div>
                </div>
                <div class="column is-half-desktop">
                    <div class="box" style="height: 100%">
                        <h6 class="is-uppercase title is-6 is-spaced">Backups Report</h6>
                        <div v-show="hasBackups">
                            <div class="chart-container">
                                <canvas id="backupsChart"></canvas>
                            </div>
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-info">{{ user.files_backups_count }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Files</small>
                                        </p>
                                    </div>
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-link">{{ user.database_backups_count }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Databases</small>
                                        </p>
                                    </div>
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-success">{{ fullBackupsTotal }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Full</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <no-results v-show="!hasBackups" style="padding-top: 1.5rem;">
                            <template slot="icon">
                                <i class="fa fa-chart-pie fa-5x"></i>
                            </template>
                        </no-results>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="slide-next">
            <div class="columns" v-if="activeTab === 'backups'">
                <div class="column">
                    <backups :user_id="this.user.id"></backups>
                </div>
            </div>
        </transition>

        <transition name="slide-next">
            <div class="columns" v-if="activeTab === 'tasks'">
                <div class="column">
                    <tasks :user_id="this.user.id"></tasks>
                </div>
            </div>
        </transition>

        <transition name="slide-next">
            <div class="columns" v-if="activeTab === 'activities'">
                <div class="column">
                    <div class="box">
                        <h2 class="is-uppercase title is-6">Recent Activity</h2>
                        <hr>
                        <activity
                                :user="id"
                                :page="activitiesPage"
                                :total.sync="totalActivities"
                                :count.sync="activitiesCount"  />
                        <hr>
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
        </transition>
    </base-layout>
</template>

<script>
    import Vue from "vue"
    import store from '../../store'
    import {LOADING} from '../../store/mutation-types'
    import {formatSize} from '../../helpers'
    import Chart from 'chart.js'
    import Activity from '../../components/activities/Activity'
    import Backups from '../../components/backups/Backups'
    import Tasks from '../../components/tasks/Tasks'
    import EditModal from '../../pages/users/EditModal'

    export default {
        name: "Profile",

        props: ['id'],

        components: {Activity, Backups, Tasks},

        data() {
            return {
                user: {},
                activeTab: 'overview',
                totalActivities: 0,
                activitiesCount: 0,
                activitiesPage: 1,
            }
        },

        computed: {
            hasBackups() {
                return !!this.user.backups_count;
            },

            hasTasks() {
                return !!this.user.tasks_count;
            },

            inactiveTasksTotal() {
                if (this.user.tasks_count) {
                    return this.user.tasks_count - this.user.active_tasks_count;
                }

                return 0;
            },

            fullBackupsTotal() {
                if (this.user.backups_count) {
                    return this.user.backups_count - (this.user.files_backups_count + this.user.database_backups_count);
                }

                return 0;
            },
        },

        methods: {
            editProfile() {
                const options = {
                    parent: this,
                    component: EditModal,
                    hasModalCard: true,
                    props: {
                        id: this.id
                    },
                };

                this.$modal.open(options);
            },
            
            initCharts() {
                const options = {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        position: 'right'
                    }
                };

                let tasksStats = [];
                tasksStats.push(this.user.active_tasks_count || 0);
                tasksStats.push(this.user.tasks_count - tasksStats[0]);
                new Chart('tasksChart', {
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: tasksStats,
                            backgroundColor: ['#23d160', '#ec3c4e'],
                        }],
                        labels: ['Active', 'Inactive']
                    },
                    options: options
                });

                let backupsStats = [];
                backupsStats.push(this.user.files_backups_count || 0);
                backupsStats.push(this.user.database_backups_count || 0);
                backupsStats.push(this.user.backups_count - (backupsStats[0] + backupsStats[1]));
                new Chart('backupsChart', {
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: backupsStats,
                            backgroundColor: ['#209cee', '#0062ff', '#23d160'],
                        }],
                        labels: ['Files', 'Databases', 'Full']
                    },
                    options: options
                });
            },

            formatSpace(space) {
                if (space) {
                    return formatSize(space);
                }

                return 0;
            },

            loadMoreActivities() {
                this.activitiesPage++;
            },
        },

        beforeRouteEnter (to, from, next) {
            if (to.params.id !== undefined) {
                store.commit(LOADING, true);
                Vue.axios.get('users/' + to.params.id + '/profile')
                    .then((response) => {
                        next((vm) => {
                            vm.user = response.data;
                            vm.initCharts();
                        });
                    });
            } else {
                next();
            }
        }
    }
</script>

<style scoped>
    .chart-container {
        height: 150px;
        margin-bottom: 1rem;
    }
</style>