<template>
    <div class="resources-table">
        <div class="level table-tools"
             v-show="search || hasFilters || checkedRows.length">
            <div class="level-left">
                <div class="level-item" v-if="search">
                    <b-field>
                        <b-input @input="onSearch"
                                 v-model="params.searchValue"
                                 :placeholder="searchPlaceholder"
                                 type="search"
                                 icon="search">
                        </b-input>
                    </b-field>
                </div>
                <div class="level-item is-hidden-desktop" v-show="checkedRows.length">
                    <button class="button is-danger is-outlined is-fullwidth" type="button"
                            @click.prevent="bulkDelete">
                        <b-icon icon="trash-alt" size="is-small"></b-icon>
                        <span>Delete ({{ checkedRows.length }})</span>
                    </button>
                </div>
            </div>
            <div class="level-right is-hidden-mobile" v-show="checkedRows.length">
                <div class="level-item">
                    <button class="button is-danger is-outlined" type="button"
                            @click.prevent="bulkDelete">
                        <b-icon icon="trash-alt" size="is-small"></b-icon>
                        <span>Delete ({{ checkedRows.length }})</span>
                    </button>
                </div>
            </div>
            <div class="level-right" v-show="hasFilters && !checkedRows.length">
                <div class="level-item" v-show="isFiltered">
                    <b-field>
                        <button type="button" class="button is-text" @click="clearFilters()">
                            <b-icon icon="times-circle" size="is-small"></b-icon>
                            <span>Clear</span>
                        </button>
                    </b-field>
                </div>
                <div class="level-item" v-show="hasFilters">
                    <b-dropdown position="is-bottom-left" class="is-block-mobile">
                        <button class="button is-light" aria-haspopup="true" aria-controls="dropdown-menu" slot="trigger">
                            <b-icon size="is-small" icon="sliders-h" />
                            <span>Filters</span>
                        </button>

                        <b-dropdown-item custom paddingless>
                            <form>
                                <div class="modal-card" style="width:250px;">
                                    <section class="modal-card-body">
                                        <slot name="filters"></slot>
                                    </section>
                                </div>
                            </form>
                        </b-dropdown-item>
                    </b-dropdown>
                </div>
            </div>
        </div>

        <b-table
                class="b-data-table"
                :data="data"
                :loading="loading"
                hoverable

                paginated
                backend-pagination
                :total="total"
                :per-page="perPage"
                @page-change="onPageChange"

                backend-sorting
                :default-sort="[params.sortField, params.sortOrder]"
                @sort="onSort"

                :checked-rows.sync="checkedRows"
                :checkable="checkable"

                :detailed="detailed"
                :detail-key="detailKey"
        >
            <template slot-scope="props">
                <slot name="table-fields" v-bind="props"></slot>
            </template>

            <template slot="detail" slot-scope="props">
                <slot name="detail" v-bind="props"></slot>
            </template>

            <template slot="empty">
                <section class="section">
                    <no-results>
                        <template slot="icon">
                            <i class="far fa-list-alt fa-5x"></i>
                        </template>
                    </no-results>
                </section>
            </template>

            <template slot="bottom-left">
                <div class="level is-mobile" v-show="total">
                    <div class="level-item">
                        <b-field>
                            <b-select :placeholder="perPage.toString()" v-model="perPage">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                            </b-select>
                        </b-field>
                    </div>
                    <div class="level-item">of</div>
                    <div class="level-item">
                        <span class="has-text-info has-text-weight-semibold">{{ total }}</span>
                    </div>
                    <div class="level-item">total items</div>
                </div>
            </template>
        </b-table>
    </div>
</template>

<script>
    import debounce from 'lodash/debounce'
    import { LOADING } from '../store/mutation-types'

    export default {
        name: "ResourcesTable",

        props: {
            api: {
                type: String,
                required: true
            },
            params: {
                type: Object,
                default: function () {
                    return {
                        sortField: '',
                        sortOrder: '',
                        searchField: '',
                        searchValue: null
                    }
                }
            },
            filters: {
                type: Object
            },
            warnMessage: {
                type: String,
                default: "Do you want to deleted the selected rows ?"
            },
            search: {
                type: Boolean,
                default: true
            },
            searchPlaceholder: {
                type: String,
                default: 'Search'
            },
            checkable: {
                type: Boolean,
                default: true
            },
            detailed: {
                type: Boolean,
                default: false
            },
            detailKey: {
                type: String,
                default: 'id'
            },
        },

        data() {
            return {
                checkedRows: [],
                data: [],
                total: 0,
                loading: false,
                perPage: 10,
                isFiltered: false,
            }
        },

        watch: {
            perPage() {
                this.getData();
            },

            filters: {
                handler: function() {
                    this.getData();
                    this.checkIfFiltered();
                },
                deep: true
            },
        },

        computed: {
            hasFilters() {
                return this.filters && Object.keys(this.filters).length;
            }
        },

        methods: {
            clearFilters() {
                for (let item of Object.keys(this.filters)) {
                    this.filters[item] = null;
                }
            },

            checkIfFiltered() {
                if (this.filters) {
                    for (let item of Object.keys(this.filters)) {
                        if (this.filters[item]) {
                            this.isFiltered = true;
                            return;
                        }
                    }
                }

                this.isFiltered = false;
            },

            getData() {
                this.checkedRows = [];
                this.loading = true;
                let params = Object.assign({}, {
                    page: this.page,
                    perPage: this.perPage,
                }, this.filters, this.params);

                this.axios.get(this.api, {params: params})
                    .then(response => {
                        this.data = response.data.data;
                        this.total = response.data.total;
                        this.loading = false;
                        this.$emit('data-updated', this.data);
                    })
                    .catch((error) => {
                        this.data = [];
                        this.total = 0;
                        this.loading = false;
                        this.$emit('data-updated', []);
                    })
            },

            onPageChange(page) {
                this.page = page;
                this.getData();
            },

            onSort(field, order) {
                this.params.sortField = field;
                this.params.sortOrder = order;
                this.getData();
            },

            onSearch: debounce(function() {
                this.getData();
            }, 500),

            bulkDelete() {
                if (confirm(this.warnMessage)) {
                    this.$store.commit(LOADING, true);
                    this.axios.post(this.api + '/delete', { items: this.checkedRows }).then(() => {
                        this.checkedRows = [];
                        this.getData();
                    });
                }
            },
        },

        mounted() {
            this.getData();
            this.checkIfFiltered();
        }
    }
</script>