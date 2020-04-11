<template>
    <base-layout>
        <div class="box">
            <div class="columns is-multiline">
                <div class="column is-7-tablet">
                    <p class="subtitle is-4 has-text-centered-mobile">
                        <span class="has-text-grey">#{{ task.id }}</span> - {{ task.name }}
                    </p>
                </div>
                <div class="column is-5-tablet has-text-right has-text-centered-mobile">
                    <b-field>
                        <router-link
                                :to="{ name: 'edit_task', params: {id: $route.params.id} }"
                                class="button is-link">
                            <b-icon icon="edit" size="is-small" />
                            <span>Edit</span>
                        </router-link>
                    </b-field>
                </div>
                <div class="column is-12">
                    <div class="columns">
                        <div class="column is-2-desktop">
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <div class="level-item">
                                        <span class="heading is-marginless">Status:</span>
                                    </div>
                                    <div class="level-item">
                                        <span class="tag" :class="[task.status ? 'is-success' : 'is-danger']">
                                            {{ task.status ? 'Active' : 'Disabled' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-4-desktop">
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <div class="level-item">
                                        <span class="heading is-marginless">Last Execution:</span>
                                    </div>
                                    <div class="level-item">
                                        <span class="has-text-weight-semibold">{{ formatToLocalDateTime(task.executed_at) || '---' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="level">
                                <div class="level-left">
                                    <div class="level-item">
                                        <span class="heading is-marginless">Token:</span>
                                    </div>
                                    <div class="level-item">
                                        <span class="has-text-weight-semibold">{{ task.token }}</span>
                                    </div>
                                    <div class="level-item">
                                        <a title="Get new token" class="has-text-info"
                                           @click.prevent="getNewToken()"><i class="fas fa-undo"></i></a>
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
                            <span class="tag is-rounded">{{ task.backups_count }}</span>
                        </a>
                    </li>
                    <li :class="{'is-active': activeTab === 'logs'}">
                        <a @click="activeTab = 'logs'">
                            <span>Logs</span>
                            <span class="tag is-rounded">{{ task.logs_count }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <transition name="slide-next">
            <div class="columns is-multiline" v-show="activeTab === 'overview'">
                <div class="column is-12">
                    <div class="box has-background-light">
                        <p class="heading has-text-centered">Execution Url</p>
                        <div class="field has-addons">
                            <div class="control is-expanded">
                                <input id="executionUrl" class="input is-primary" type="text"
                                       :value="getExecutionUrl(task.token)" readonly>
                            </div>
                            <div class="control">
                                <button class="button is-primary copy" title="Copy to clipboard" data-clipboard-target="#executionUrl">
                                    <b-icon icon="copy" size="is-small" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="card">
                        <div class="card-content">
                            <h6 class="is-uppercase title is-6 is-spaced">Memory usage</h6>
                            <div class="level is-mobile" v-if="task.statistics.length">
                                <div class="level-left">
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-danger">{{ maxMemoryUsage }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Max</small>
                                        </p>
                                    </div>
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-success">{{ minMemoryUsage }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Min</small>
                                        </p>
                                    </div>
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-link">{{ avgMemoryUsage }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Average</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <no-results v-if="!task.statistics.length">
                                <template slot="icon">
                                    <i class="fa fa-chart-line fa-5x"></i>
                                </template>
                            </no-results>
                        </div>
                        <div style="height: 150px" v-show="task.statistics.length">
                            <canvas id="memoryUsageChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="card">
                        <div class="card-content">
                            <h6 class="is-uppercase title is-6 is-spaced">Execution time</h6>
                            <div class="level is-mobile" v-if="task.statistics.length">
                                <div class="level-left">
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-danger">{{ maxExecutionTime }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Max</small>
                                        </p>
                                    </div>
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-success">{{ minExecutionTime }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Min</small>
                                        </p>
                                    </div>
                                    <div class="level-item">
                                        <p class="subtitle is-4">
                                            <span class="has-text-link">{{ avgExecutionTime }}</span>
                                            <small class="has-text-grey is-uppercase is-size-6">Average</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <no-results v-if="!task.statistics.length">
                                <template slot="icon">
                                    <i class="fa fa-chart-line fa-5x"></i>
                                </template>
                            </no-results>
                        </div>
                        <div style="height: 150px" v-show="task.statistics.length">
                            <canvas id="executionTimeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="slide-next">
            <div class="columns" v-if="activeTab === 'backups'">
                <div class="column">
                    <backups :task_id="task.id"></backups>
                </div>
            </div>
        </transition>

        <transition name="slide-next">
            <div class="columns" v-if="activeTab === 'logs'">
                <div class="column">
                    <logs :task_id="$route.params.id"
                          :api="'tasks/' + $route.params.id + '/logs'"></logs>
                </div>
            </div>
        </transition>
    </base-layout>
</template>

<script>
    import Vue from "vue"
    import store from '../../store'
    import { LOADING } from '../../store/mutation-types'
    import Logs from './Logs'
    import Backups from '../../components/backups/Backups'
    import ClipboardJS from 'clipboard'
    import { Line } from 'chart.js'

    export default {
        components: {Logs, Backups},

        data() {
            return {
                task: {
                    statistics: []
                },
                activeTab: 'overview'
            }
        },

        computed: {
            memoryUsageArray() {
                let data = [];
                for (let item of this.task.statistics) {
                    data.push((parseInt(item.memory_used) / 1000000).toFixed(1));
                }

                return data;
            },

            executionTimeArray() {
                let data = [];
                for (let item of this.task.statistics) {
                    data.push((parseFloat(item.execution_time)).toFixed(1));
                }

                return data;
            },

            maxMemoryUsage() {
                if (this.memoryUsageArray.length) {
                    return Math.max(...this.memoryUsageArray);
                }

                return 0;
            },

            minMemoryUsage() {
                if (this.memoryUsageArray.length) {
                    return Math.min(...this.memoryUsageArray);
                }

                return 0;
            },

            avgMemoryUsage() {
                if (this.memoryUsageArray.length) {
                    let sum = this.memoryUsageArray.reduce((a, b) => { return parseFloat(a) + parseFloat(b); });
                    return (sum / this.memoryUsageArray.length).toFixed(1);
                }

                return 0;
            },

            maxExecutionTime() {
                if (this.executionTimeArray.length) {
                    return Math.max(...this.executionTimeArray);
                }

                return 0;
            },

            minExecutionTime() {
                if (this.executionTimeArray.length) {
                    return Math.min(...this.executionTimeArray);
                }

                return 0;
            },

            avgExecutionTime() {
                if (this.executionTimeArray.length) {
                    let sum = this.executionTimeArray.reduce((a, b) => { return parseFloat(a) + parseFloat(b); });
                    return (sum / this.executionTimeArray.length).toFixed(1);
                }

                return 0;
            },
        },

        methods: {
            initCharts() {
                const options = {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    scales: {
                        xAxes: [{
                            display: false,
                            gridLines: {
                                display:false
                            }
                        }],
                        yAxes: [{
                            display: false,
                            ticks: {
                                beginAtZero: true,
                            },
                            gridLines: {
                                display:false
                            }
                        }],
                    }
                };

                let stats = this.getMemoryStats();
                let ctx = document.getElementById('memoryUsageChart').getContext("2d");
                let gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(0, 98, 155, .5)');
                gradient.addColorStop(1, '#FFFFFF');
                new Line(ctx, {
                    data: {
                        labels: stats.labels,
                        datasets: [{
                            label: 'MB',
                            data: stats.data,
                            backgroundColor: gradient,
                            borderColor: 'rgba(0, 98, 155, .8)',
                            borderWidth: 2
                        }]
                    },
                    options: options
                });

                stats = this.getExecutionStats();
                ctx = document.getElementById('executionTimeChart').getContext("2d");
                gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(32, 156, 238, .5)');
                gradient.addColorStop(1, '#FFFFFF');
                new Line(ctx, {
                    data: {
                        labels: stats.labels,
                        datasets: [{
                            label: 'seconds',
                            data: stats.data,
                            backgroundColor: gradient,
                            borderColor: 'rgba(32, 156, 238, .8)',
                            borderWidth: 2
                        }]
                    },
                    options: options
                });
            },

            getMemoryStats() {
                const labels = [], data = [];
                for (let item of this.task.statistics) {
                    labels.push('');
                    data.push((parseInt(item.memory_used) / 1000000).toFixed(1));
                }

                // make sure the chart line starts
                if (data.length === 1) {
                    data.unshift(0);
                    data.push(0);
                }

                return { labels, data: data };
            },

            getExecutionStats() {
                const labels = [], data = [];
                for (let item of this.task.statistics) {
                    labels.push('');
                    data.push((parseFloat(item.execution_time)).toFixed(1));
                }

                // make sure the chart line starts
                if (data.length === 1) {
                    data.unshift(0);
                    data.push(0);
                }

                return { labels, data: data };
            },

            getExecutionUrl(token) {
                return window.APP.baseUrl + '/tasks/run/' + token;
            },

            getNewToken() {
                if (confirm("Are you sure to change the task token ?")) {
                    this.$store.commit(LOADING, true);
                    this.axios.put('tasks/' + this.task.id + '/token').then((response) => {
                        this.task.token = response.data.token;
                    });
                }
            }
        },

        beforeRouteEnter(to, from, next) {
            store.commit(LOADING, true);
            Vue.axios.get('tasks/' + to.params.id + '/overview')
                .then((response) => {
                    next(vm => {
                        vm.$set(vm.$data, 'task', response.data);
                        vm.initCharts();
                    });
                });
        },

        mounted() {
            new ClipboardJS('.copy');
        }
    }
</script>