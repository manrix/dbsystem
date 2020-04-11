<template>
    <base-layout>
        <template slot="pageToolsRight">
            <b-field>
                <button class="button is-link"
                        @click.prevent="editDestination(null)">
                    <b-icon icon="plus" size="is-small"></b-icon>
                    <span>New Destination</span>
                </button>
            </b-field>
        </template>

        <resources-table
                ref="table"
                :api="api"
                :params="params"
                :filters="filters"
                :warn-message="warnMessage"
                search-placeholder="Search for destinations"
        >
            <template slot="filters">
                <b-field>
                    <b-select expanded v-model="filters.driver" placeholder="Driver">
                        <option v-for="(driver, slug) in destinationDrivers"
                                :value="slug"
                                :key="slug">
                            {{ driver }}
                        </option>
                    </b-select>
                </b-field>
            </template>

            <template slot="table-fields" slot-scope="props">
                <b-table-column field="id" label="#" class="has-text-grey has-text-weight-semibold w-8" sortable centered>
                    {{ props.row.id }}
                </b-table-column>

                <b-table-column field="name" label="Name" sortable>
                    {{ props.row.name }}
                </b-table-column>

                <b-table-column field="driver" label="Driver" sortable>
                    <b-tooltip animated type="is-dark" position="is-left"
                               :label="destinationDrivers[props.row.driver]">
                        <font-awesome-icon
                                :icon="driverIcons[props.row.driver]"
                                :class="driverClasses[props.row.driver]" />
                    </b-tooltip>
                </b-table-column>

                <b-table-column field="root" label="Path" centered>
                    {{ props.row.root || '/' }}
                </b-table-column>

                <b-table-column field="created_at" label="Modified" sortable centered>
                    <span class="tag">{{ formatToLocalDateTime(props.row.updated_at) || '---' }}</span>
                </b-table-column>

                <b-table-column class="has-text-right-desktop w-10">
                    <button title="Edit" class="button is-transparent has-text-info"
                            @click.prevent="editDestination(props.row.id)">
                        <b-icon icon="edit" size="is-small" />
                    </button>
                    <button title="Delete" class="button is-transparent has-text-danger"
                            @click.prevent="deleteDestination(props.row.id)">
                        <b-icon icon="trash-alt" size="is-small" />
                    </button>
                </b-table-column>
            </template>
        </resources-table>
    </base-layout>
</template>

<script>
    import ResourcesTable from '../../components/ResourcesTable'
    import EditModal from '../../pages/destinations/EditModal'
    import { mapGetters } from 'vuex'
    import { LOADING } from '../../store/mutation-types'

    export default {
        components: {ResourcesTable},

        data() {
            return {
                api: 'destinations',
                params: {
                    searchField: 'name',
                    searchValue: null,
                },
                filters: {
                    driver: null,
                },
                warnMessage: "Do you want to removed the selected destinations ?",
                driverIcons: {
                    'dropbox': ['fab', 'dropbox'],
                    'g3': ['fab', 'google-drive'],
                    'ftp': ['fas', 'globe'],
                    'local': ['fas', 'server'],
                },
                driverClasses: {
                    'dropbox': 'has-text-link',
                    'g3': 'has-text-primary',
                    'ftp': 'has-text-info',
                    'local': 'has-text-grey-dark',
                },
            }
        },

        computed: {
            ...mapGetters({
                destinationDrivers: 'destinationDrivers',
            }),
        },

        methods: {
            editDestination(id) {
                const options = {
                    parent: this,
                    component: EditModal,
                    hasModalCard: true,
                    events: {
                        update: () => {
                            this.$refs.table.getData();
                        }
                    }
                };

                if (id) {
                    Object.assign(options, {
                        props: {
                            id: id
                        },
                    });
                }

                this.$modal.open(options);
            },

            deleteDestination(id) {
                if (!confirm("Are you sure to remove the destination ?")) {
                    return;
                }

                this.$store.commit(LOADING, true);
                this.axios.delete('destinations/' + id).then(() => {
                    this.$refs.table.getData();
                });
            }
        },
    }
</script>