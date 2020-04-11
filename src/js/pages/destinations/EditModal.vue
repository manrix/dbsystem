<template>
    <form @submit.prevent="saveDestination">
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
                                        v-model="destination.driver"
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
                        <label class="label">Name <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="A custom name to identify the destination"
                                        v-model="destination.name"
                                        type="text"
                                        required>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field" v-show="canViewType(['ftp'])">
                        <label class="label">Host <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Ftp host ip or domain"
                                        v-model="destination.host"
                                        type="text"
                                        :required="canViewType(['ftp'])">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field" v-show="canViewType(['ftp'])">
                        <label class="label">User <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Ftp account user"
                                        type="text"
                                        v-model="destination.user"
                                        :required="canViewType(['ftp'])">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field" v-show="canViewType(['ftp'])">
                        <label class="label">Password <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Ftp account password"
                                        v-model="destination.password"
                                        type="password"
                                        :required="canViewType(['ftp'])"
                                        password-reveal>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field" v-show="canViewType(['ftp'])">
                        <label class="label">Port</label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Ftp connection port"
                                        v-model="destination.port"
                                        type="number">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field" v-show="canViewType(['dropbox'])">
                        <label class="label">Token <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Dropbox api token"
                                        v-model="destination.token"
                                        type="password"
                                        :required="canViewType(['dropbox'])"
                                        password-reveal>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field" v-show="canViewType(['g3'])">
                        <label class="label">Client ID <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Google api client id"
                                        v-model="destination.client_id"
                                        type="text"
                                        :required="canViewType(['g3'])">
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field" v-show="canViewType(['g3'])">
                        <label class="label">Client Secret <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Google api client secret"
                                        v-model="destination.client_secret"
                                        type="password"
                                        :required="canViewType(['g3'])"
                                        password-reveal>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field" v-show="canViewType(['g3'])">
                        <label class="label">Refresh Token <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Google api refresh token"
                                        v-model="destination.refresh_token"
                                        type="password"
                                        :required="canViewType(['g3'])"
                                        password-reveal>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Root</label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        placeholder="Destination root path"
                                        v-model="destination.root"
                                        type="text">
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
                            <b-icon icon="save" size="is-small" />
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
                destination: {
                    driver: null,
                    name: null,
                    host: null,
                    port: null,
                    user: null,
                    password: null,
                    root: null,
                    client_id: null,
                    client_secret: null,
                    refresh_token: null,
                    token: null,
                },
                driverIcons: {
                    'dropbox': ['fab', 'dropbox'],
                    'g3': ['fab', 'google-drive'],
                    'ftp': ['fas', 'globe'],
                    'local': ['fas', 'server'],
                },
                buttonClasses: {
                    'dropbox': 'is-link',
                    'g3': 'is-primary',
                    'ftp': 'is-info',
                    'local': 'is-dark',
                },
            }
        },

        computed: {
            ...mapGetters({
                drivers: 'destinationDrivers',
            }),

            isEdit() {
                return !!this.id;
            },

            pageTitle() {
                return !this.isEdit ? 'New Destination' : 'Edit Destination #' + this.id;
            },

            isValid() {
                let validation = true;
                switch (this.destination.driver) {
                    case 'ftp':
                        validation = this.destination.host && this.destination.user && this.destination.password;
                        break;
                    case 'g3':
                        validation = this.destination.client_id && this.destination.client_secret && this.destination.refresh_token;
                        break;
                    case 'dropbox':
                        validation = this.destination.token;
                        break;
                }

                return !!(this.destination.name && this.destination.driver && validation);
            }
        },

        methods: {
            saveDestination() {
                this.$store.commit(LOADING, true);
                if (this.isEdit) {
                    this.axios.put('destinations/' + this.id, this.destination).then((response) => {
                        this.$emit('update');
                        this.$emit('close');
                    });
                } else {
                    this.axios.post('destinations', this.destination).then((response) => {
                        this.$emit('update');
                        this.$emit('close');
                    });
                }
            },

            canViewType(types) {
                return types.indexOf(this.destination.driver) >= 0;
            }
        },

        mounted() {
            this.destination.driver = Object.keys(this.drivers)[0];
            if (this.id) {
                this.loading = true;
                this.axios.get('destinations/' + this.id)
                    .then((response) => {
                        Object.assign(this.destination, response.data);
                        this.loading = false;
                    });
            }

        }
    }
</script>