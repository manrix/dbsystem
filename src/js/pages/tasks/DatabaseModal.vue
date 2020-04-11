<template>
    <form @submit.prevent="onSubmit">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ modalTitle }}</p>
            </header>

            <section class="modal-card-body">
                <b-field label="Database" label-for="searchTerm">
                    <b-select v-model="data.database"
                              placeholder="Select a database"
                              :loading="loading"
                              required
                              expanded>
                        <option
                                v-for="database in databases"
                                :value="database"
                                :key="database.id">
                            {{ database.name }} ({{ drivers[database.driver] }})
                        </option>
                    </b-select>
                </b-field>

                <b-field label="Options"></b-field>
                <b-field v-show="canViewField(['mysql'])">
                    <b-checkbox v-model="data.skip_comments">Skip comments</b-checkbox>
                </b-field>
                <b-field v-show="canViewField(['mysql'])">
                    <b-checkbox v-model="data.use_extended_inserts">Use extended inserts</b-checkbox>
                </b-field>
                <b-field v-show="canViewField(['mysql'])">
                    <b-checkbox v-model="data.use_single_transaction">Use single transaction</b-checkbox>
                </b-field>
                <b-field v-show="canViewField(['pgsql'])">
                    <b-checkbox v-model="data.use_inserts">Use inserts</b-checkbox>
                </b-field>
                <b-field>
                    <b-checkbox
                            v-model="data.use_compression"
                            message="The backup will be compressed using gzip">Use compression</b-checkbox>
                </b-field>

                <div v-if="connected">
                    <b-field label="Tables"></b-field>
                    <b-field
                            grouped
                            group-multiline
                            v-if="data.tables.length">
                        <div class="control"
                             v-for="(table, index) in data.tables"
                             :key="index">
                            <b-tag type="is-info"
                                   attached
                                   closable
                                   @close="removeTable(index)">
                                {{ table }}
                            </b-tag>
                        </div>
                    </b-field>
                    <p v-else>No table in the database</p>
                </div>
            </section>

            <footer class="modal-card-foot is-justified-center">
                <div class="field is-grouped">
                    <p class="control" v-show="connected">
                        <button type="submit" class="button is-success">
                            <span>{{ isEdit ? 'Save' : 'Add' }}</span>
                        </button>
                    </p>
                    <p class="control" v-show="!connected">
                        <button type="button" class="button is-link"
                                :disabled="!hasDatabase"
                                :class="{'is-loading': connecting}"
                                @click="connectDatabase">
                            <span>Connect</span>
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

    export default {
        name: "DatabaseModal",

        props: {
            database: Object,
        },

        data() {
            return {
                data: {
                    database: null,
                    tables: [],
                    skip_comments: false,
                    use_extended_inserts: false,
                    use_single_transaction: false,
                    use_inserts: false,
                    use_compression: false,
                },
                databases: [],
                loading: false,
                connecting: false,
                connected: false,
            }
        },

        computed: {
            ...mapGetters({
                drivers: 'databaseDrivers',
            }),

            isEdit() {
                return !!Object.keys(this.database).length;
            },

            modalTitle() {
                return (this.isEdit ? 'Edit' : 'Add') + ' database';
            },

            hasDatabase() {
                return this.data.database && this.data.database.id;
            }
        },

        methods: {
            connectDatabase() {
                this.connecting = true;
                this.connected = false;
                this.axios.get('databases/' + this.data.database.id + '/connect').then((response) => {
                    if (!this.isEdit) {
                        this.data.tables = response.data;
                    }
                    this.connecting = false;
                    this.connected = true;
                }).catch((error) => {
                    this.connecting = false;
                });
            },

            removeTable(index) {
                this.data.tables.splice(index, 1);
            },

            canViewField(drivers) {
                return this.connected && drivers.indexOf(this.data.database.driver) >= 0;
            },

            onSubmit() {
                this.$emit('submit', {data: this.data});
                this.$emit('close');
            }
        },

        mounted() {
            if (Object.keys(this.database).length) {
                Object.assign(this.data, this.database);
                this.connectDatabase();
            }

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