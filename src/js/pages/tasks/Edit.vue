<template>
    <base-layout>
        <form @submit.prevent="saveTask">
            <div class="columns is-multiline">
                <div class="column is-5-desktop is-4-widescreen is-6-tablet">
                    <div class="box">
                        <div class="level">
                            <div class="level-left">
                                <div class="level-item">
                                    <b-switch v-model="task.status" type="is-success">
                                        {{ statusText }}
                                    </b-switch>
                                </div>
                            </div>
                            <div class="level-right">
                                <div class="level-item">
                                    <b-field>
                                        <button type="submit" class="button is-success"
                                                :disabled="!isValid">
                                            <b-icon icon="save" size="is-small" />
                                            <span>Save</span>
                                        </button>
                                    </b-field>
                                </div>
                                <div class="level-item">
                                    <b-field>
                                        <button type="button" class="button is-light"
                                                @click="resetData()">
                                            <span>Cancel</span>
                                        </button>
                                    </b-field>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <b-field label="Name">
                            <b-input
                                    v-model="task.name"
                                    placeholder="A name for the task"
                                    type="text"
                                    required>
                            </b-input>
                        </b-field>
                        <hr>
                        <b-field label="Notes" label-for="task_description">
                            <b-input
                                    id="task_description"
                                    v-model="task.description"
                                    type="textarea"
                                    placeholder="Additional notes">
                            </b-input>
                        </b-field>
                    </div>
                </div>

                <div class="column is-7-desktop is-8-widescreen">
                    <div class="box">
                        <b-field grouped>
                            <b-field label="Backup name" expanded>
                                <b-input
                                        v-model="task.file_name"
                                        placeholder="The name of your backup"
                                        required>
                                </b-input>
                            </b-field>
                            <b-field label="Compression" expanded class="is-hidden-mobile">
                                <b-select v-model="task.compression" required>
                                    <option value="tar">Tar (faster)</option>
                                    <option value="zip">Zip</option>
                                </b-select>
                            </b-field>
                        </b-field>

                        <b-field label="Compression" expanded class="is-hidden-tablet">
                            <b-select v-model="task.compression" required>
                                <option value="tar">Tar (faster)</option>
                                <option value="zip">Zip</option>
                            </b-select>
                        </b-field>

                        <b-field>
                            <b-checkbox v-model="task.file_name_timestamp">
                                Add timestamp
                            </b-checkbox>
                        </b-field>

                        <div v-show="task.file_name">
                            <hr>
                            <p class="heading">Preview</p>
                            <pre class="has-text-danger">{{ backupName }}</pre>
                        </div>
                    </div>

                    <div class="card">
                        <div class="tabs is-fullwidth is-marginless has-background-light">
                            <ul>
                                <li :class="{'is-active': activeTab === 'databases'}">
                                    <a @click.prevent="activeTab = 'databases'">
                                        <span class="icon is-small"><i class="fas fa-database"></i></span>
                                        <span>Databases</span>
                                    </a>
                                </li>
                                <li :class="{'is-active': activeTab === 'files'}">
                                    <a @click.prevent="activeTab = 'files'">
                                        <span class="icon is-small"><i class="far fa-file"></i></span>
                                        <span>Files</span>
                                    </a>
                                </li>
                                <li :class="{'is-active': activeTab === 'destinations'}">
                                    <a @click.prevent="activeTab = 'destinations'">
                                        <span class="icon is-small"><i class="fas fa-cloud"></i></span>
                                        <span>Destinations</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-content" v-show="activeTab === 'databases'">
                            <b-table :mobile-cards="false" :data="task.databases" hoverable>
                                <template slot-scope="props">
                                    <b-table-column field="name" label="Name" sortable>
                                        {{ props.row.database.name }}
                                    </b-table-column>

                                    <b-table-column field="driver" label="Driver" sortable>
                                        {{ databaseDrivers[props.row.database.driver] }}
                                    </b-table-column>

                                    <b-table-column field="host" label="Host" sortable>
                                        {{ props.row.database.host }}
                                    </b-table-column>

                                    <b-table-column style="text-align: right;">
                                        <button title="Edit" type="button" class="button is-transparent has-text-info"
                                                @click="editDatabase(props.index)">
                                            <b-icon icon="edit" size="is-small"></b-icon>
                                        </button>
                                        <button title="Remove" type="button" class="button is-transparent has-text-danger"
                                                @click="removeDatabase(props.index)">
                                            <b-icon icon="trash-alt" size="is-small"></b-icon>
                                        </button>
                                    </b-table-column>
                                </template>

                                <template slot="empty">
                                    <div class="has-text-centered">
                                        No database added
                                    </div>
                                </template>

                                <template slot="footer">
                                    <div class="buttons is-centered">
                                        <button class="button is-link is-small"
                                                type="button"
                                                @click="openNewDatabaseModal()">
                                            <b-icon icon="plus" size="is-small" />
                                            <span>Add</span>
                                        </button>
                                    </div>
                                </template>
                            </b-table>
                        </div>

                        <div class="card-content" v-show="activeTab === 'files'">
                            <b-message type="is-info" size="is-small">
                                You can specify files and directories to exclude using php regular expressions or patterns if use select the option to use shell.
                            </b-message>
                            <div class="columns">
                                <div class="column is-6-desktop">
                                    <b-field>
                                        <b-input v-model="fileToInclude"
                                                 expanded
                                                 placeholder="File to include"
                                                 icon="plus">
                                        </b-input>
                                        <p class="control">
                                            <button class="button is-link" type="button"
                                                    :disabled="!fileToInclude"
                                                    @click.prevent="addFileToInclude">Add</button>
                                        </p>
                                    </b-field>
                                    <table class="table is-bordered is-narrow is-hoverable is-fullwidth">
                                        <tbody>
                                        <tr v-for="(file, index) in task.file.include">
                                            <td>
                                                <b-field>
                                                    <b-input size="is-small" v-model="task.file.include[index]"></b-input>
                                                </b-field>
                                            </td>
                                            <td style="width: 5%">
                                                <a role="button" title="Remove"
                                                   @click.prevent="removeIncludedFile(index)">
                                                    <b-icon icon="times" class="has-text-danger" />
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="column is-6-desktop">
                                    <b-field>
                                        <b-input v-model="fileToExclude"
                                                 expanded
                                                 placeholder="File to exclude"
                                                 icon="minus">
                                        </b-input>
                                        <p class="control">
                                            <button class="button is-link" type="button"
                                                    :disabled="!fileToExclude"
                                                    @click.prevent="addFileToExclude">Add</button>
                                        </p>
                                    </b-field>
                                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                        <tbody>
                                        <tr v-for="(file, index) in task.file.exclude">
                                            <td>
                                                <b-field>
                                                    <b-input size="is-small" v-model="task.file.exclude[index]"></b-input>
                                                </b-field>
                                            </td>
                                            <td style="width: 5%">
                                                <a role="button" title="Remove" @click.prevent="removeExcludedFile(index)">
                                                    <b-icon icon="times" class="has-text-danger" />
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <b-field message="The zip and tar shell commands will be used to backup files">
                                <b-checkbox v-model="task.use_shell">
                                    Use shell commands
                                </b-checkbox>
                            </b-field>
                        </div>

                        <div v-show="activeTab === 'destinations'">
                            <div class="card-content">
                                <b-table :mobile-cards="false" :data="task.destinations" hoverable >
                                    <template slot-scope="props">
                                        <b-table-column field="name" label="Name" sortable>
                                            {{ props.row.destination.name }}
                                        </b-table-column>

                                        <b-table-column field="driver" label="Driver" sortable>
                                            {{ destinationDrivers[props.row.destination.driver] }}
                                        </b-table-column>

                                        <b-table-column field="path" label="Path" sortable>
                                            {{ props.row.path || '/' }}
                                        </b-table-column>

                                        <b-table-column style="text-align: right;">
                                            <button title="Edit" type="button" class="button is-transparent has-text-info"
                                                    @click="editDestination(props.index)">
                                                <b-icon icon="edit" size="is-small"></b-icon>
                                            </button>
                                            <button title="Remove" type="button" class="button is-transparent has-text-danger"
                                                    @click="removeDestination(props.index)">
                                                <b-icon icon="trash-alt" size="is-small"></b-icon>
                                            </button>
                                        </b-table-column>
                                    </template>

                                    <template slot="empty">
                                        <div class="has-text-centered">
                                            No destination added
                                        </div>
                                    </template>

                                    <template slot="footer">
                                        <div class="buttons is-centered">
                                            <button class="button is-link is-small"
                                                    type="button"
                                                    @click="openNewDestinationModal()">
                                                <b-icon icon="plus" size="is-small" />
                                                <span>Add</span>
                                            </button>
                                        </div>
                                    </template>
                                </b-table>
                                <hr>
                                <b-field message="The backup will be deleted from local disk">
                                    <b-checkbox v-model="task.not_save_locally">
                                        Don't save locally
                                    </b-checkbox>
                                </b-field>
                                <b-field>
                                    <b-checkbox v-model="task.send_to_email">
                                        Send backup to email
                                    </b-checkbox>
                                </b-field>
                                <b-field v-if="task.send_to_email">
                                    <b-input
                                            v-model="task.email"
                                            type="email"
                                            placeholder="Email to send backup to"
                                            icon="envelope"
                                            required>
                                    </b-input>
                                </b-field>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </base-layout>
</template>

<script>
    import WarnUnsaved from "../../mixins/WarnUnsaved"
    import DatabaseModal from "./DatabaseModal"
    import DestinationModal from "./DestinationModal"
    import Vue from "vue"
    import store from '../../store'
    import { mapGetters } from 'vuex'
    import { LOADING } from '../../store/mutation-types'

    export default {
        props: ['id'],
        
        mixins: [WarnUnsaved],

        data() {
            return {
                originalData: {},
                databaseToEdit: {},
                databaseToEditIndex: null,
                destinationToEdit: {},
                destinationToEditIndex: null,
                fileToInclude: null,
                fileToExclude: null,
                activeTab: 'databases',
                task: {
                    name: null,
                    status: true,
                    file_name: null,
                    file_name_timestamp: true,
                    not_save_locally: false,
                    send_to_email: false,
                    email: null,
                    databases: [],
                    file: {
                        include: [],
                        exclude: [],
                    },
                    destinations: [],
                    description: null,
                    compression: 'tar',
                    use_shell: false,
                }
            }
        },

        computed: {
            statusText() {
                return this.task.status ? 'Enabled' : 'Disabled';
            },

            ...mapGetters([
                'databaseDrivers', 'destinationDrivers'
            ]),

            backupName() {
                const date = new Date();
                const dateString = date.getFullYear() + '-' + ("0" + (date.getUTCMonth() + 1)).slice(-2) + '-' + ("0" + date.getDate()).slice(-2);
                const timeString=  ("0" + date.getHours()).slice(-2) + '-' + ("0" + date.getMinutes()).slice(-2)  + '-' + ("0" + date.getSeconds()).slice(-2);

                let name = this.task.file_name;
                if (this.task.file_name_timestamp) {
                    name += '_' + dateString + '_' + timeString;
                }
                if (this.task.compression) {
                    name += '.' + this.task.compression;
                }

                return name;
            },

            isEdit() {
                return !!this.id;
            },

            isValid() {
                return this.task.name &&
                    this.task.file_name &&
                    (this.task.databases.length || this.task.file.include.length);
            }
        },

        methods: {
            saveTask() {
                this.$store.commit(LOADING, true);
                if (this.isEdit) {
                    this.axios.put('tasks/' + this.id, this.task).then();
                } else {
                    this.axios.post('tasks', this.task).then((response) => {
                        this.$router.push({ name: 'edit_task', params: { id: response.data.task.id }})
                    });
                }
            },

            openNewDatabaseModal() {
                this.$modal.open({
                    parent: this,
                    component: DatabaseModal,
                    hasModalCard: true,
                    props: {
                        database: this.databaseToEdit
                    },
                    onCancel: this.onDatabaseModalClose,
                    events: {
                        close: this.onDatabaseModalClose,
                        submit: this.saveDatabase,
                    }
                });
            },

            saveDatabase(event) {
                if (Object.keys(this.databaseToEdit).length) {
                    this.$set(this.task.databases, this.databaseToEditIndex, event.data);
                } else {
                    this.task.databases.push(event.data);
                }
            },

            editDatabase(index) {
                this.databaseToEditIndex = index;
                this.databaseToEdit = this.task.databases[index];
                this.openNewDatabaseModal();
            },

            removeDatabase(index) {
                this.task.databases.splice(index, 1);
            },

            openNewDestinationModal() {
                this.$modal.open({
                    parent: this,
                    component: DestinationModal,
                    hasModalCard: true,
                    props: {
                        destination: this.destinationToEdit
                    },
                    onCancel: this.onDestinationModalClose,
                    events: {
                        close: this.onDestinationModalClose,
                        submit: this.saveDestination,
                    }
                });
            },

            saveDestination(event) {
                if (Object.keys(this.destinationToEdit).length) {
                    this.$set(this.task.destinations, this.destinationToEditIndex, event.data);
                } else {
                    this.task.destinations.push(event.data);
                }
            },

            editDestination(index) {
                this.destinationToEditIndex = index;
                this.destinationToEdit = this.task.destinations[index];
                this.openNewDestinationModal();
            },

            removeDestination(index) {
                this.task.destinations.splice(index, 1);
            },

            onDatabaseModalClose() {
                this.databaseToEditIndex = null;
                this.databaseToEdit = {};
            },

            onDestinationModalClose() {
                this.destinationToEditIndex = null;
                this.destinationToEdit = {};
            },

            setData(data) {
                Object.assign(this.task, data);
                if (!this.task.file) {
                    this.task.file = {
                        include: [],
                        exclude: [],
                    }
                }

                this.determineActiveTab();
            },

            addFileToInclude() {
                this.task.file.include.push(this.fileToInclude);
                this.fileToInclude = null;
            },

            addFileToExclude() {
                this.task.file.exclude.push(this.fileToExclude);
                this.fileToExclude = null;
            },

            removeIncludedFile(index) {
                this.task.file.include.splice(index, 1);
            },

            removeExcludedFile(index) {
                this.task.file.exclude.splice(index, 1);
            },

            determineActiveTab() {
                if (this.task.databases.length) {
                    this.activeTab = 'databases';
                } else if (this.task.file.include.length) {
                    this.activeTab = 'files';
                }
            },

            resetData() {
                Object.assign(this.task, this.originalData);
            },
        },

        created() {
            const data = Object.assign({}, this.task);
            Object.assign(this.originalData, data);
        },

        beforeRouteUpdate(to, from, next) {
            this.$store.commit(LOADING, true);
            this.axios.get('tasks/' + to.params.id)
                .then(response => {
                    this.setData(response.data);
                    Object.assign(this.originalData, response.data);
                    next();
                });
        },

        beforeRouteEnter(to, from, next) {
            if (to.params.id !== undefined) {
                store.commit(LOADING, true);
                Vue.axios.get('tasks/' + to.params.id)
                    .then((response) => {
                        next(vm => {
                            vm.setData(response.data);
                            Object.assign(vm.originalData, response.data);
                        });
                    });
            } else {
                next();
            }
        },
    }
</script>