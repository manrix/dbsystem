<template>
    <form @submit.prevent="saveDatabase">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ pageTitle }}</p>
            </header>

            <section class="modal-card-body" style="min-height: 150px;">
                <b-loading :is-full-page="false" :active.sync="loading" />
                <div v-show="!loading">
                    <div class="field">
                        <label class="label">Driver <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-select
                                        placeholder="Select a driver"
                                        v-model="database.driver"
                                        expanded
                                        required>
                                    <option v-for="(driver, slug) in drivers"
                                            :value="slug"
                                            :key="slug">
                                        {{ driver }}
                                    </option>
                                </b-select>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Database Name <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Your database name"
                                        v-model="database.name"
                                        type="text"
                                        required>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Host <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Ex. localhost"
                                        v-model="database.host"
                                        type="text"
                                        required>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Port</label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Ex. 3306"
                                        v-model="database.port"
                                        type="number">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">User <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Ex. root"
                                        type="text"
                                        v-model="database.user"
                                        required>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Your database password"
                                        v-model="database.password"
                                        type="password"
                                        password-reveal>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="modal-card-foot is-justified-center">
                <div class="field is-grouped">
                    <p class="control">
                        <button type="submit" class="button is-success"
                                :disabled="!isValid">
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
    import { mapGetters } from 'vuex'
    import { LOADING } from '../../store/mutation-types'

    export default {
        name: "EditModal",

        props: ['id'],

        data() {
            return {
                loading: false,
                database: {
                    name: null,
                    driver: null,
                    host: 'localhost',
                    port: null,
                    user: null,
                    password: null
                },
                driverIcons: {
                    'mysql': 'icon-mysql',
                    'postgresql': 'icon-postgres',
                    'sqlite': 'fa fa-database',
                },
            }
        },

        computed: {
            ...mapGetters({
                drivers: 'databaseDrivers',
            }),

            isEdit() {
                return !!this.id;
            },

            pageTitle() {
                return !this.isEdit ? 'New Database' : 'Edit Database #' + this.id;
            },

            isValid() {
                return this.database.name &&
                    this.database.driver &&
                    this.database.host &&
                    this.database.user;
            }
        },

        methods: {
            saveDatabase() {
                this.$store.commit(LOADING, true);
                if (this.isEdit) {
                    this.axios.put('databases/' + this.id, this.database).then((response) => {
                        this.$emit('update');
                        this.$emit('close');
                    });
                } else {
                    this.axios.post('databases', this.database).then((response) => {
                        this.$emit('update');
                        this.$emit('close');
                    });
                }
            }
        },

        mounted() {
            this.database.driver = Object.keys(this.drivers)[0];
            if (this.id) {
                this.loading = true;
                this.axios.get('databases/' + this.id)
                    .then((response) => {
                        Object.assign(this.database, response.data);
                        this.loading = false;
                    });
            }

        }
    }
</script>