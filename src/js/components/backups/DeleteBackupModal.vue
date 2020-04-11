<template>
    <form @submit.prevent="deleteBackup">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Delete Backup #{{ backup.id }}</p>
            </header>

            <section class="modal-card-body">
                <b-message type="is-info">
                    Select the destinations from which you want to delete the backup.
                </b-message>
                <div v-if="backup.saved_locally">
                    <b-field>
                        <b-checkbox v-model="data.delete_local">
                            Delete from local backups directory
                        </b-checkbox>
                    </b-field>
                    <hr>
                </div>
                <b-field label="Backup destinations">
                    <b-table class="b-data-table"
                             :data="backup.destinations"
                             :checked-rows.sync="data.destinations"
                             checkable>
                        <template slot-scope="props">
                            <b-table-column field="name" label="Name" sortable>
                                {{ props.row.destination.name }}
                            </b-table-column>

                            <b-table-column field="driver" label="Driver" sortable>
                                {{ drivers[props.row.destination.driver] }}
                            </b-table-column>

                            <b-table-column field="path" label="Path" sortable>
                                {{ props.row.path || '-' }}
                            </b-table-column>
                        </template>

                        <template slot="empty">
                            The backup doesn't have other destinations
                        </template>
                    </b-table>
                </b-field>
            </section>

            <footer class="modal-card-foot is-justified-center">
                <div class="field is-grouped">
                    <p class="control">
                        <button type="submit" class="button is-danger" :disabled="!isValid">
                            <span>Delete</span>
                        </button>
                    </p>
                    <p class="control">
                        <button type="button" @click="$parent.close()" class="button">
                            <span>Close</span>
                        </button>
                    </p>
                </div>
            </footer>
        </div>
    </form>
</template>

<script>
    import { LOADING } from '../../store/mutation-types'
    import { mapGetters } from 'vuex'

    export default {
        props: {
            backup: {
                type: Object,
                required: true
            },
        },

        data() {
            return {
                data: {
                    destinations: [],
                    delete_local: false
                }
            }
        },

        computed: {
            ...mapGetters({
                drivers: 'destinationDrivers',
            }),

            isValid() {
                return (this.backup.saved_locally && this.data.delete_local) ||
                    this.data.destinations.length;
            }
        },

        methods: {
            deleteBackup() {
                this.$store.commit(LOADING, true);
                this.axios.post('backups/' + this.backup.id + '/delete', this.data).then(() => {
                    this.$emit('deleted');
                    this.$parent.close();
                });
            }
        },
    }
</script>