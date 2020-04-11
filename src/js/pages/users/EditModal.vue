<template>
    <form @submit.prevent="saveUser">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ pageTitle }}</p>
            </header>

            <section class="modal-card-body" style="min-height: 150px;">
                <b-loading :is-full-page="false" :active.sync="loading" />
                <div v-show="!loading">
                    <div class="field">
                        <label class="label">Name <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input id="userName"
                                         placeholder="User name"
                                         v-model="user.name" type="text"
                                         required>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Email <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input id="userEmail"
                                         placeholder="User email address"
                                         v-model="user.email"
                                         type="email"
                                         required>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Password <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        id="userPassword"
                                        placeholder="*******"
                                        v-model="user.password"
                                        type="password"
                                        password-reveal
                                        autocomplete="off"
                                        required>
                                </b-input>
                            </b-field>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Confirm Password <span class="has-text-danger">*</span></label>
                        <div class="field-body">
                            <b-field>
                                <b-input
                                        id="userPasswordConfirmation"
                                        placeholder="*******"
                                        v-model="user.password_confirmation"
                                        type="password"
                                        password-reveal
                                        autocomplete="off"
                                        required>
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
    import {LOADING, UPDATE_USER} from '../../store/mutation-types'

    export default {
        name: "EditModal",

        props: ['id'],

        data() {
            return {
                loading: false,
                user: {
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null
                }
            }
        },

        computed: {
            isEdit() {
                return !!this.id;
            },

            pageTitle() {
                return !this.isEdit ? 'New User' : 'Edit User #' + this.id;
            },

            isValid() {
                return this.user.name &&
                    this.user.email &&
                    this.user.password &&
                    this.user.password_confirmation;
            }
        },

        methods: {
            saveUser() {
                this.$store.commit(LOADING, true);
                if (this.isEdit) {
                    this.axios.put('users/' + this.id, this.user).then((response) => {
                        // if is the same of logged user, update store user data
                        if (response.data.user.id === window.APP.user.id) {
                            window.APP.user = response.data.user;
                            this.$store.commit(UPDATE_USER, response.data.user);
                        }
                        this.$emit('update');
                        this.$emit('close');
                    });
                } else {
                    this.axios.post('users', this.user).then((response) => {
                        this.$emit('update');
                        this.$emit('close');
                    });
                }
            }
        },

        mounted() {
            if (this.id) {
                this.loading = true;
                this.axios.get('users/' + this.id)
                    .then((response) => {
                        Object.assign(this.user, response.data);
                        this.loading = false;
                    });
            }

        }
    }
</script>