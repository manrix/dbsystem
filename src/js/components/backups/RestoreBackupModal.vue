<template>
    <form @submit.prevent="restoreBackup">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Restore Backup #{{ backup.id }}</p>
            </header>

            <section class="modal-card-body">
                <b-message type="is-info">
                    Specify the path to the files inside the backup archive
                    corresponding to database and files backups to restore.
                </b-message>
                <b-message type="is-warning" size="is-small">
                    Paths are relative to the application directory.
                </b-message>
                <b-field>
                    <b-checkbox v-model="data.self_backup" @input="resetData()">
                        File itself is the backup to restore
                    </b-checkbox>
                </b-field>
                <b-field>
                    <b-input
                            v-show="data.self_backup"
                            v-model="data.destination_path"
                            placeholder="Destination path">
                    </b-input>
                </b-field>

                <div v-if="!data.self_backup">
                    <hr>
                    <b-collapse class="card" :open.sync="databasesActive">
                        <div slot="trigger" slot-scope="props" class="card-header">
                            <p class="card-header-title">Databases</p>
                            <a class="card-header-icon">
                                <b-icon v-show="props.open" icon="caret-down"></b-icon>
                                <b-icon v-show="!props.open" icon="caret-up"></b-icon>
                            </a>
                        </div>
                        <table class="table is-fullwidth">
                            <thead>
                            <tr>
                                <th>Path in archive</th>
                                <th>Database</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-show="!data.databases.length">
                                <td colspan="3">Add a database to restore</td>
                            </tr>
                            <tr v-for="(database, index) in data.databases">
                                <td>
                                    <b-field>
                                        <b-input
                                                v-model="database.archive_path"
                                                placeholder="ex. databases/backup.zip"
                                                expanded
                                                required>
                                        </b-input>
                                    </b-field>
                                </td>
                                <td>
                                    <b-field>
                                        <b-select v-model="database.database"
                                                  placeholder="Select a database"
                                                  :loading="loading"
                                                  required>
                                            <option
                                                    v-for="database in databases"
                                                    :value="database"
                                                    :key="database.id">
                                                {{ database.name }} ({{ drivers[database.driver] }})
                                            </option>
                                        </b-select>
                                    </b-field>
                                </td>
                                <td>
                                    <button type="button" title="Remove" class="button is-transparent has-text-danger"
                                            @click="removeDatabase(index)">
                                        <b-icon icon="times" size="is-small" />
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3">
                                    <button type="button" class="button is-link is-small"
                                            @click.prevent="addDatabase">
                                        Add
                                    </button>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </b-collapse>

                    <hr>

                    <b-collapse class="card" :open.sync="filesActive">
                        <div slot="trigger" slot-scope="props" class="card-header">
                            <p class="card-header-title">Files</p>
                            <a class="card-header-icon">
                                <b-icon v-show="props.open" icon="caret-down"></b-icon>
                                <b-icon v-show="!props.open" icon="caret-up"></b-icon>
                            </a>
                        </div>
                        <table class="table is-fullwidth">
                            <thead>
                            <tr>
                                <th>Path in archive</th>
                                <th>Destination path</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-show="!data.files.length">
                                <td colspan="3">Add a file to restore</td>
                            </tr>
                            <tr v-for="(file, index) in data.files">
                                <td>
                                    <b-field>
                                        <b-input
                                                v-model="file.archive_path"
                                                placeholder="ex. files/mybackup.zip"
                                                expanded
                                                required>
                                        </b-input>
                                    </b-field>
                                </td>
                                <td>
                                    <b-field>
                                        <b-input
                                                v-model="file.destination_path"
                                                placeholder="ex. ../public/"
                                                expanded
                                                required>
                                        </b-input>
                                    </b-field>
                                </td>
                                <td>
                                    <button type="button" title="Remove" class="button is-transparent has-text-danger"
                                            @click="removeFile(index)">
                                        <b-icon icon="times" size="is-small"></b-icon>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3">
                                    <button type="button" class="button is-link is-small"
                                            @click.prevent="addFile">
                                        Add
                                    </button>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </b-collapse>
                </div>
            </section>

            <footer class="modal-card-foot is-justified-center">
                <div class="field is-grouped">
                    <p class="control">
                        <button type="submit" class="button is-success" :disabled="!isValid">
                            <span>Restore</span>
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
    import { mapGetters } from 'vuex'
    import { LOADING } from '../../store/mutation-types'

    export default {
        props: {
            backup: {
                type: Object,
                required: true
            },
            manifest: {
                type: [Object, Array],
                required: true
            },
        },

        data() {
            return {
                data: {
                    self_backup: false,
                    destination_path: null,
                    databases: [],
                    files: []
                },
                databases: [],
                loading: false,
                databasesActive: false,
                filesActive: false,
            }
        },

        computed: {
            ...mapGetters({
                drivers: 'databaseDrivers',
            }),

            isValid() {
                return (this.data.self_backup && this.data.destination_path) ||
                    (this.data.databases.length || this.data.files.length);
            },
        },

        methods: {
            restoreBackup() {
                this.$store.commit(LOADING, true);
                this.axios.post('backups/' + this.backup.id + '/restore', this.data).then();
                this.$parent.close();
            },

            addDatabase() {
                this.data.databases.push({
                    archive_path: null,
                    database: null
                });
            },

            removeDatabase(index) {
                this.data.databases.splice(index, 1);
            },

            addFile() {
                this.data.files.push({
                    archive_path: null,
                    destination_path: null
                });
            },

            removeFile(index) {
                this.data.files.splice(index, 1);
            },

            resetData() {
                this.data.databases = [];
                this.data.files = [];
            }
        },

        mounted() {
            if (this.manifest.databases && this.manifest.databases.length) {
                for (const item of this.manifest.databases) {
                    this.data.databases.push({
                        archive_path: item,
                        database: null
                    });
                }
            }

            if (this.manifest.files && this.manifest.files.length) {
                for (const item of this.manifest.files) {
                    this.data.files.push({
                        archive_path: item,
                        destination_path: null
                    });
                }
            }

            this.databasesActive = !this.backup.type || !!this.data.databases.length;
            this.filesActive = !this.backup.type || !!this.data.files.length;

            this.loading = true;
            this.axios.get('databases/list')
                .then(response => {
                    this.databases = response.data;
                    this.loading = false;
                })
                .catch((error) => {
                    this.databases = [];
                    this.loading = false;
                });
        }
    }
</script>

<style scoped>
    .table td {
        vertical-align: middle;
    }
</style>