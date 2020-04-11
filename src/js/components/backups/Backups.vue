<template>
    <resources-table
            ref="table"
            :api="api"
            :params="params"
            :filters="filters"
            :warn-message="warnMessage"
            search-placeholder="Search for backups"
            @data-updated="onTableDataUpdated"
    >
        <template slot="filters">
            <b-field>
                <b-select expanded placeholder="Type" v-model="filters.type">
                    <option value="files">File</option>
                    <option value="database">Database</option>
                    <option value="full">Full</option>
                </b-select>
            </b-field>
            <b-field>
                <b-select expanded placeholder="Storage" v-model="filters.storage">
                    <option v-for="(driver, slug) in destinationDrivers"
                            :value="slug"
                            :key="slug">
                        {{ driver }}
                    </option>
                </b-select>
            </b-field>
        </template>

        <template slot="table-fields" slot-scope="props">
            <b-table-column field="name" label="Name" sortable>
                <a v-if="props.row.saved_locally"
                   :href="getDownloadUrl(props.row.id)"
                   target="_blank"
                   :title="'Download ' + props.row.name">{{ props.row.name }}</a>
                <span v-else>{{ props.row.name }}</span>
            </b-table-column>

            <b-table-column field="created_at" label="Created" sortable centered>
                <span class="tag is-light">{{ formatToLocalDateTime(props.row.created_at) }}</span>
            </b-table-column>

            <b-table-column field="type" label="Type" sortable centered>
                <b-tooltip :label="props.row.type|capitalize"
                           type="is-dark"
                           animated
                           position="is-left">
                    <font-awesome-icon
                            :icon="typeIcons[props.row.type]"
                            :class="props.row.type === 'full' ? 'has-text-success' : 'has-text-info'" />
                </b-tooltip>
            </b-table-column>

            <b-table-column label="Storage" centered>
                <div class="tags">
                    <b-tooltip v-if="props.row.saved_locally"
                               label="Local"
                               type="is-dark"
                               animated
                               position="is-left">
                        <span class="tag">
                            <font-awesome-icon :icon="driverIcons['local']" />
                        </span>
                    </b-tooltip>
                    <b-tooltip v-for="item in props.row.destinations" :key="item.id"
                               :label="destinationDrivers[item.destination.driver]"
                               type="is-dark"
                               animated
                               position="is-left">
                            <span class="tag">
                                <font-awesome-icon :icon="driverIcons[item.destination.driver]" />
                            </span>
                    </b-tooltip>
                </div>
            </b-table-column>

            <b-table-column field="size" label="Size" class="has-text-right-desktop" sortable centered>
                <span class="has-text-info">{{ getFormattedSize(props.row.size) }}</span>
            </b-table-column>

            <b-table-column class="has-text-right-desktop">
                <button title="Transfer" type="button" class="button is-transparent has-text-link"
                        @click="uploadBackup(props.index)"
                        v-if="props.row.saved_locally">
                    <b-icon icon="cloud-upload-alt" size="is-small"></b-icon>
                </button>
                <button title="Restore" type="button" class="button is-transparent has-text-success"
                        @click="restoreBackup(props.index)"
                        v-if="props.row.saved_locally">
                    <b-icon icon="undo" size="is-small"></b-icon>
                </button>
                <button title="Delete" type="button" class="button is-transparent has-text-danger"
                        @click="deleteBackup(props.index)">
                    <b-icon icon="trash-alt" size="is-small"></b-icon>
                </button>
            </b-table-column>
        </template>
    </resources-table>
</template>

<script>
    import ResourcesTable from '../ResourcesTable'
    import RestoreBackup from './RestoreBackupModal'
    import UploadBackup from './UploadBackupModal'
    import DeleteBackup from './DeleteBackupModal'
    import { LOADING } from '../../store/mutation-types'
    import { formatSize } from '../../helpers'
    import { mapGetters } from 'vuex'
    import BCheckbox from "buefy/src/components/checkbox/Checkbox";

    export default {
        components: {BCheckbox, ResourcesTable},

        props: {
            task_id: {
                type: Number
            },
            user_id: {
                type: Number
            },
        },

        data() {
            return {
                data: [],
                importActive: false,
                uploadActive: false,
                restoreActive: false,
                backupToUpload: null,
                backupToRestore: null,
                manifestToRestore: null,
                deleteActive: false,
                backupToDelete: null,
                api: 'backups',
                params: {
                    task: null,
                    user: null,
                    searchField: 'name',
                    searchValue: null,
                },
                filters: {
                    type: null,
                    storage: null,
                },
                warnMessage: "Do you want to deleted the selected backups ? They will be removed from any destination.",
                driverIcons: {
                    'dropbox': ['fab', 'dropbox'],
                    'g3': ['fab', 'google-drive'],
                    'ftp': ['fas', 'globe'],
                    'local': ['fas', 'server'],
                },
                typeIcons: {
                    'files': ['far', 'file'],
                    'database': ['fas', 'database'],
                    'full': ['fas', 'check-double'],
                },
            }
        },

        computed: {
            ...mapGetters({
                destinationDrivers: 'destinationDrivers',
            }),
        },

        methods: {
            getData() {
                this.$refs.table.getData();
            },

            onTableDataUpdated(data) {
                Object.assign(this.data, data);
            },

            getDownloadUrl(id) {
                return window.APP.baseUrl + '/backups/' + id + '/download';
            },

            restoreBackup(index) {
                this.$store.commit(LOADING, true);
                this.axios.get('backups/' + this.data[index].id + '/manifest')
                    .then(response => {
                        this.backupToRestore = this.data[index];
                        this.manifestToRestore = response.data;
                        this.$modal.open({
                            parent: this,
                            component: RestoreBackup,
                            hasModalCard: true,
                            props: {
                                backup: this.backupToRestore,
                                manifest: this.manifestToRestore
                            }
                        });
                    });
            },

            uploadBackup(index) {
                this.backupToUpload = this.data[index];
                this.$modal.open({
                    parent: this,
                    component: UploadBackup,
                    hasModalCard: true,
                    props: {
                        backup: this.backupToUpload,
                    },
                    events: {
                        uploaded: () => {
                            this.getData();
                        }
                    }
                });
            },

            deleteBackup(index) {
                if (this.data[index].destinations && this.data[index].destinations.length) {
                    this.backupToDelete = this.data[index];
                    this.$modal.open({
                        parent: this,
                        component: DeleteBackup,
                        hasModalCard: true,
                        props: {
                            backup: this.backupToDelete
                        },
                        events: {
                            deleted: () => {
                                this.getData();
                            }
                        }
                    });
                } else {
                    if (!confirm("Are you sure to remove the backup ?")) {
                        return;
                    }

                    this.$store.commit(LOADING, true);
                    this.axios.post('backups/' + this.data[index].id + '/delete', {'delete_local': true}).then(() => {
                        this.getData();
                    });
                }
            },

            getFormattedSize(size) {
                return formatSize(size);
            }
        },

        created() {
            this.params.task = this.task_id || null;
            this.params.user = this.user_id || null;
        }
    }
</script>

<style scoped>
    .tags .tooltip:not(:last-child) {
        margin-right: 0.5rem;
    }
</style>