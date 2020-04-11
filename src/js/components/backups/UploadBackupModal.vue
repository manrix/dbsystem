<template>
    <form @submit.prevent="uploadBackup">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Upload Backup #{{ backup.id }}</p>
            </header>

            <section class="modal-card-body">
                <b-message type="is-info">
                    Transfer the backup to any of saved destinations.
                </b-message>
                <b-field label="Destination" label-for="searchTerm">
                    <b-select id="searchTerm"
                              v-model="data.destination"
                              placeholder="Select a destination"
                              :loading="loading"
                              :required="!data.send_to_email"
                              expanded>
                        <option
                                v-for="destination in destinations"
                                :value="destination"
                                :key="destination.id">
                            {{ destination.name }} ({{ drivers[destination.driver] }})
                        </option>
                    </b-select>
                </b-field>
                <b-field label="Path" label-for="upload_path">
                    <b-input id="upload_path"
                             v-model="data.path"
                             name="path"
                             placeholder="An optional alternative path">
                    </b-input>
                </b-field>
                <b-field message="The destination will be added to backup destinations table">
                    <b-checkbox v-model="data.save" :disabled="!data.destination">
                        Associate destination with this backup
                    </b-checkbox>
                </b-field>

                <hr>

                <b-field>
                    <b-checkbox v-model="data.send_to_email">
                        Send backup to email
                    </b-checkbox>
                </b-field>
                <b-field v-if="data.send_to_email">
                    <b-input
                            v-model="data.email"
                            type="email"
                            placeholder="Email to send backup to"
                            icon="envelope"
                            required>
                    </b-input>
                </b-field>
            </section>

            <footer class="modal-card-foot is-justified-center">
                <div class="field is-grouped">
                    <p class="control">
                        <button type="submit" class="button is-link"
                                :disabled="!data.destination && !data.send_to_email">
                            <span>Upload</span>
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
            }
        },

        data() {
            return {
                data: {
                    destination: null,
                    path: null,
                    save: true,
                    send_to_email: false,
                    email: null,
                },
                term: null,
                destinations: [],
                loading: false,
            }
        },

        computed: {
            ...mapGetters({
                drivers: 'destinationDrivers',
            }),
        },

        methods: {
            uploadBackup() {
                this.$store.commit(LOADING, true);
                this.axios.post('backups/' + this.backup.id + '/transfer', this.data).then(() => {
                    this.$parent.close();
                    this.$emit('uploaded');
                });
            },
        },

        mounted() {
            this.loading = true;
            this.axios.get('destinations/list')
                .then(response => {
                    this.destinations = response.data;
                    this.loading = false;
                })
                .catch((error) => {
                    this.databases = [];
                    this.loading = false;
                });
        }
    }
</script>