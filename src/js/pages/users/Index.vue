<template>
    <base-layout>
        <template slot="pageToolsRight">
            <b-field>
                <button class="button is-link"
                        @click.prevent="editUser(null)">
                    <b-icon icon="plus" size="is-small"></b-icon>
                    <span>New User</span>
                </button>
            </b-field>
        </template>

        <resources-table
                ref="table"
                :api="api"
                :params="params"
                :warn-message="warnMessage"
                search-placeholder="Search for users"
        >
            <template slot="table-fields" slot-scope="props">
                <b-table-column field="id" label="#" class="has-text-grey has-text-weight-semibold w-8" sortable centered>
                    {{ props.row.id }}
                </b-table-column>

                <b-table-column field="name" label="Name" sortable>
                    <router-link :to="{ name: 'user_profile', params: {id: props.row.id} }" title="View Profile">
                        {{ props.row.name }}
                    </router-link>
                </b-table-column>

                <b-table-column field="email" label="Email" sortable>
                    {{ props.row.email }}
                </b-table-column>

                <b-table-column field="updated_at" label="Modified" class="w-15" sortable centered>
                    <span class="tag is-light">{{ formatToLocalDateTime(props.row.updated_at) || '---' }}</span>
                </b-table-column>

                <b-table-column class="w-15 has-text-right-desktop">
                    <router-link :to="{ name: 'user_profile', params: {id: props.row.id} }"
                                 title="View Profile" class="button is-transparent has-text-link">
                        <b-icon icon="eye" size="is-small" />
                    </router-link>
                    <button title="Edit" class="button is-transparent has-text-info"
                            @click.prevent="editUser(props.row.id)">
                        <b-icon icon="edit" size="is-small" />
                    </button>
                    <button class="button is-transparent has-text-danger" title="Delete"
                            @click.prevent="deleteUser(props.row.id)">
                        <b-icon icon="trash-alt" size="is-small" />
                    </button>
                </b-table-column>
            </template>
        </resources-table>
    </base-layout>
</template>

<script>
    import ResourcesTable from '../../components/ResourcesTable'
    import { LOADING } from '../../store/mutation-types'
    import EditModal from '../../pages/users/EditModal'

    export default {
        components: {ResourcesTable},

        data() {
            return {
                api: 'users',
                params: {
                    searchField: ['name', 'email'],
                    searchValue: null,
                },
                warnMessage: "Do you want to remove the selected users ?"
            }
        },

        methods: {
            editUser(id) {
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

            deleteUser(id) {
                if (!confirm("Are you sure to remove the user ?")) {
                    return;
                }

                this.$store.commit(LOADING, true);
                this.axios.delete('users/' + id).then(() => {
                    this.$refs.table.getData();
                });
            },
        }
    }
</script>