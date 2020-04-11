import debounce from 'lodash/debounce'

export default {
    data() {
        return {
            checkedRows: [],
            data: [],
            total: 0,
            loading: false,
            params: {
                page: 1,
                perPage: 15,
                sortField: '',
                sortOrder: '',
                searchField: '',
                searchValue: null
            },
        }
    },

    methods: {
        getData() {
            this.loading = true;
            this.axios.get(this.api, {params: this.params})
                .then(response => {
                    this.data = response.data.data;
                    this.total = response.data.total;
                    this.loading = false
                })
                .catch((error) => {
                    this.data = [];
                    this.total = 0;
                    this.loading = false;
                })
        },

        onPageChange(page) {
            this.params.page = page;
            this.getData();
        },

        onSort(field, order) {
            this.params.sortField = field;
            this.params.sortOrder = order;
            this.getData();
        },

        onSearch: debounce(function() {
            this.getData();
        }, 500)
    },

    mounted() {
        this.getData();
    }
};