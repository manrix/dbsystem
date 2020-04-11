<template>
    <base-layout>
        <template slot="pageToolsRight">
            <b-field>
                <button class="button is-link"
                        @click.prevent="editDatabase(null)">
                    <b-icon icon="plus" size="is-small" />
                    <span>New Database</span>
                </button>
            </b-field>
        </template>

        <resources-table
                ref="table"
                :api="api"
                :params="params"
                :filters="filters"
                :warn-message="warnMessage"
                search-placeholder="Search for databases"
        >
            <template slot="filters">
                <b-field>
                    <b-select expanded v-model="filters.driver" placeholder="Driver">
                        <option v-for="(driver, slug) in databaseDrivers"
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
                    <b-tooltip
                            animated
                            v-if="props.row.driver === 'mysql'"
                            type="is-dark"
                            position="is-left"
                            :label="databaseDrivers[props.row.driver]">
                        <i :class="driverIcons[props.row.driver]"></i>
                    </b-tooltip>
                    <b-tooltip
                            animated
                            v-else-if="props.row.driver === 'postgresql'"
                            type="is-dark"
                            position="is-left"
                            :label="databaseDrivers[props.row.driver]">
                        <i :class="driverIcons[props.row.driver]"></i>
                    </b-tooltip>
                    <b-tooltip
                            animated
                            v-else
                            type="is-dark"
                            position="is-left"
                            :label="databaseDrivers[props.row.driver]">
                        <font-awesome-icon :icon="['fas','database']" class="has-text-grey-dark" />
                    </b-tooltip>
                </b-table-column>

                <b-table-column field="host" label="Host" sortable>
                    {{ props.row.host }}
                </b-table-column>

                <b-table-column field="user" label="User" sortable>
                    {{ props.row.user }}
                </b-table-column>

                <b-table-column field="created_at" label="Modified" class="w-10" sortable centered>
                    <span class="tag is-light">{{ formatToLocalDateTime(props.row.updated_at) || '---' }}</span>
                </b-table-column>

                <b-table-column class="has-text-right-desktop w-10">
                    <button title="Edit" class="button is-transparent has-text-info"
                            @click.prevent="editDatabase(props.row.id)">
                        <b-icon icon="edit" size="is-small" />
                    </button>
                    <button title="Delete" class="button is-transparent has-text-danger"
                            @click.prevent="deleteDatabase(props.row.id)">
                        <b-icon icon="trash-alt" size="is-small" />
                    </button>
                </b-table-column>
            </template>
        </resources-table>
    </base-layout>
</template>

<script>
    import ResourcesTable from '../../components/ResourcesTable'
    import EditModal from '../../pages/databases/EditModal'
    import { LOADING } from '../../store/mutation-types'
    import { mapGetters } from 'vuex'

    export default {
        components: {ResourcesTable},

        data() {
            return {
                modalActive: false,
                databaseToEdit: null,
                api: 'databases',
                params: {
                    searchField: 'name',
                    searchValue: null,
                },
                filters: {
                    driver: null,
                },
                warnMessage: "Do you want to removed the selected databases ?",
                driverIcons: {
                    'mysql': 'icon-mysql has-text-link',
                    'postgresql': 'icon-postgres has-text-info',
                },
            }
        },

        computed: {
            ...mapGetters([
                'databaseDrivers'
            ]),
        },

        methods: {
            editDatabase(id) {
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

            deleteDatabase(id) {
                if (!confirm("Are you sure to remove the database ?")) {
                    return;
                }

                this.$store.commit(LOADING, true);
                this.axios.delete('databases/' + id).then(() => {
                    this.$refs.table.getData();
                });
            }
        },
    }
</script>