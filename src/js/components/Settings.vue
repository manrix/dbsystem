<template>
    <form @submit.prevent="saveSettings">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Settings</p>
            </header>

            <section class="modal-card-body" style="min-height: 100px;">
                <b-loading :is-full-page="false" :active.sync="loading"></b-loading>

                <div v-show="!loading">
                    <div class="tabs has-background-light is-centered">
                        <ul>
                            <li :class="{'is-active': activeItem === 'backups'}">
                                <a @click.prevent="activeItem = 'backups'">Backups</a>
                            </li>
                            <li :class="{'is-active': activeItem === 'tasks'}">
                                <a @click.prevent="activeItem = 'tasks'">Tasks</a>
                            </li>
                            <li :class="{'is-active': activeItem === 'security'}">
                                <a @click.prevent="activeItem = 'security'">Security</a>
                            </li>
                        </ul>
                    </div>
                    <div v-show="activeItem === 'backups'">
                        <b-field
                                label="Max number of backups"
                                message="The max amount of backups to keep. 0 for unlimited">
                            <b-input v-model="data.max_backups"
                                     placeholder="Number of backups"
                                     type="number">
                            </b-input>
                        </b-field>
                        <hr>
                        <b-field
                                label="Delete backups older than"
                                message="Backups older than specified days will be deleted. 0 to disable it">
                            <b-field>
                                <b-input v-model="data.max_backups_days"
                                         expanded
                                         type="number">
                                </b-input>
                                <p class="control">
                                    <span class="button is-static">days</span>
                                </p>
                            </b-field>
                        </b-field>
                    </div>
                    <div v-show="activeItem === 'tasks'">
                        <b-field label="Max execution time" message="Max execution time for tasks">
                            <b-field>
                                <b-input v-model="data.max_execution_time"
                                         placeholder="Max execution time"
                                         expanded
                                         type="number">
                                </b-input>
                                <p class="control">
                                    <span class="button is-static">seconds</span>
                                </p>
                            </b-field>
                        </b-field>
                        <hr>
                        <b-field label="Memory limit" message="Memory limit for the tasks">
                            <b-field>
                                <b-input v-model="data.memory_limit"
                                         placeholder="Memory limit"
                                         expanded
                                         type="number">
                                </b-input>
                                <p class="control">
                                    <span class="button is-static">MB</span>
                                </p>
                            </b-field>
                        </b-field>
                        <hr>
                        <b-field label="Delete logs older than">
                            <b-field>
                                <b-input v-model="data.logs_days"
                                         placeholder="Number of days"
                                         expanded
                                         type="number">
                                </b-input>
                                <p class="control">
                                    <span class="button is-static">days</span>
                                </p>
                            </b-field>
                        </b-field>
                        <hr>
                        <b-field message="Enable or disable email notifications">
                            <b-field>
                                <b-checkbox true-value="1"
                                            false-value="0"
                                            v-model="data.task_notification">
                                    Notify when a task is completed
                                </b-checkbox>
                            </b-field>
                        </b-field>
                    </div>
                    <div v-show="activeItem === 'security'">
                        <b-field
                                label="Secure token"
                                message="Token to execute scheduled commands externally">
                            <b-field>
                                <b-input v-model="data.api_token"
                                         placeholder="A secure token">
                                </b-input>
                            </b-field>
                        </b-field>
                        <div class="message is-small is-info">
                            <div class="message-body">
                                The token isn't necessary if you call scheduled commands directly from cron jobs.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="modal-card-foot is-justified-center">
                <div class="field is-grouped">
                    <p class="control">
                        <button type="submit" class="button is-success" :class="{'is-loading': saving}">
                            <b-icon icon="save" size="is-small"></b-icon>
                            <span>Save</span>
                        </button>
                    </p>
                    <p class="control">
                        <button type="button" class="button"
                                @click="$parent.close()">
                            <span>Close</span>
                        </button>
                    </p>
                </div>
            </footer>
        </div>
    </form>
</template>

<script>
    export default {
        name: "Settings",

        data() {
            return {
                saving: false,
                loading: false,
                activeItem: 'backups',
                data: {
                    max_backups: 0,
                    max_backups_days: 0,
                    logs_days: 0,
                    task_notification: 1,
                    api_token: null,
                    max_execution_time: null,
                    memory_limit: null,
                }
            }
        },

        methods: {
            saveSettings() {
                this.saving = true;
                this.axios.post('settings', this.data).then(() => {
                    this.saving = false;
                }).catch(() => this.saving = false);
            }
        },

        mounted() {
            this.loading = true;
            this.axios.get('settings')
                .then((response) => {
                    Object.assign(this.data, response.data);
                    this.loading = false;
                });
        }
    }
</script>

<style scoped>
    .tabs {
        margin-left: -20px;
        margin-right: -20px;
        margin-top: -20px;
    }
</style>