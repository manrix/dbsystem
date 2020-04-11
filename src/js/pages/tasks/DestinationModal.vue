<template>
    <form @submit.prevent="onSubmit">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ modalTitle }}</p>
            </header>

            <section class="modal-card-body">
                <b-field label="Destination" label-for="searchTerm">
                    <b-select v-model="data.destination"
                              placeholder="Select a destination"
                              :loading="loading"
                              required
                              expanded>
                        <option
                                v-for="destination in destinations"
                                :value="destination"
                                :key="destination.id">
                            {{ destination.name }} ({{ drivers[destination.driver] }})
                        </option>
                    </b-select>
                </b-field>
                <b-field label="Path">
                    <b-input v-model="data.path" placeholder="Optional path"></b-input>
                </b-field>
            </section>

            <footer class="modal-card-foot is-justified-center">
                <div class="field is-grouped">
                    <p class="control">
                        <button type="submit" class="button is-success"
                                :disabled="!hasDestination">
                            <span>{{ isEdit ? 'Save' : 'Add' }}</span>
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

    export default {
        name: "DestinationModal",

        props: {
            destination: Object,
        },

        data() {
            return {
                data: {
                    destination: null,
                    path: null,
                },
                destinations: [],
                loading: false,
                connecting: false,
                connected: false,
            }
        },

        computed: {
            ...mapGetters({
                drivers: 'destinationDrivers',
            }),

            isEdit() {
                return !!Object.keys(this.destination).length;
            },

            modalTitle() {
                return (this.isEdit ? 'Edit' : 'Add') + ' destination';
            },

            hasDestination() {
                return this.data.destination && this.data.destination.id;
            }
        },

        methods: {
            onSubmit() {
                this.$emit('submit', {data: this.data});
                this.$emit('close');
            }
        },

        mounted() {
            if (Object.keys(this.destination).length) {
                Object.assign(this.data, this.destination);
            }

            this.loading = true;
            this.axios.get('destinations/list')
                .then(response => {
                    this.destinations = response.data;
                    this.loading = false;
                })
                .catch((error) => {
                    this.destinations = [];
                    this.loading = false;
                });
        }
    }
</script>