<template>
    <resources-table
            ref="table"
            :api="api"
            :params="params"
            :filters="filters"
            :warn-message="warnMessage"
            search-placeholder="Search for tasks"
    >
        <template slot="filters">
            <b-field>
                <b-select expanded v-model="filters.status" placeholder="Status">
                    <option value="0">Inactive</option>
                    <option value="1">Active</option>
                </b-select>
            </b-field>
        </template>

        <template slot="table-fields" slot-scope="props">
            <b-table-column field="id" label="#" class="has-text-grey has-text-weight-semibold w-8" sortable centered>
                {{ props.row.id }}
            </b-table-column>

            <b-table-column field="name" label="Name" sortable>
                <router-link :to="{ name: 'show_task', params: {id: props.row.id} }"
                             title="View task">{{ props.row.name }}</router-link>
            </b-table-column>

            <b-table-column field="status" label="Status" class="has-text-grey w-10" sortable>
                <span v-if="props.row.status">
                    <font-awesome-icon :icon="['fas','circle']" class="has-text-success is-size-7"></font-awesome-icon> Active
                </span>
                <span v-else>
                    <font-awesome-icon :icon="['fas','circle']" class="has-text-danger is-size-7"></font-awesome-icon> Disabled
                </span>
            </b-table-column>

            <b-table-column field="executed_at" label="Executed" class="w-15" sortable centered>
                <span class="tag is-light">{{ formatToLocalDateTime(props.row.executed_at) || '---' }}</span>
            </b-table-column>

            <b-table-column field="updated_at" label="Modified" class="w-15" sortable centered>
                <span class="tag is-light">{{ formatToLocalDateTime(props.row.updated_at) || '---' }}</span>
            </b-table-column>

            <b-table-column class="has-text-right-desktop">
                <router-link :to="{ name: 'show_task', params: {id: props.row.id} }"
                             title="View" class="button is-transparent has-text-link">
                    <b-icon icon="eye" size="is-small"></b-icon>
                </router-link>
                <button class="button is-transparent has-text-success"
                        title="Execute" @click.prevent="executeTask(props.row.token)">
                    <b-icon icon="play" size="is-small"></b-icon>
                </button>
                <router-link :to="{ name: 'edit_task', params: {id: props.row.id} }"
                             title="Edit" class="button is-transparent has-text-info">
                    <b-icon icon="edit" size="is-small"></b-icon>
                </router-link>
                <button class="button is-transparent has-text-danger"
                        title="Delete" @click.prevent="deleteTask(props.row.id)">
                    <b-icon icon="trash-alt" size="is-small"></b-icon>
                </button>
            </b-table-column>
        </template>
    </resources-table>
</template>

<script>
    import ResourcesTable from '../../components/ResourcesTable'
    import { LOADING } from '../../store/mutation-types'

    export default {
        components: {ResourcesTable},

        props: {
            user_id: {
                type: Number
            },
        },

        data() {
            return {
                api: 'tasks',
                params: {
                    searchField: 'name',
                    searchValue: null,
                    user: null,
                },
                filters: {
                    status: null,
                },
                warnMessage: "Do you want to removed the selected tasks ?"
            }
        },

        methods: {
            deleteTask(id) {
                if (!confirm("Are you sure to remove the task ?")) {
                    return;
                }

                this.$store.commit(LOADING, true);
                this.axios.delete('tasks/' + id).then(() => {
                    this.$refs.table.getData();
                });
            },

            executeTask(token) {
                if (!confirm("Do you want to execute the task now ?")) {
                    return;
                }

                this.$store.commit(LOADING, true);
                this.axios.get(this.getExecutionUrl(token)).then(() => {
                    this.$refs.table.getData();
                });
            },

            getExecutionUrl(token) {
                return window.APP.baseUrl + '/tasks/run/' + token;
            }
        },

        created() {
            this.params.user = this.user_id || null;
        }
    }
</script>