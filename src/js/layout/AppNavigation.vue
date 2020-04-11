<template>
    <header>
        <nav class="navbar is-link is-spaced has-shadow app-navigation"
             role="navigation" aria-label="main navigation">
            <div class="container">
                <div class="navbar-brand">
                    <router-link class="navbar-item logo" to="/" :title="appName">
                        <img src="img/logo.svg" :alt="appName">
                    </router-link>

                    <a title="Settings" class="navbar-item is-hidden-desktop"
                       @click="showSettings = true">
                        <i class="fas fa-cog"></i>
                    </a>
                    <a title="Activities" class="navbar-item is-hidden-desktop"
                       @click="showActivities = true">
                        <i class="far fa-list-alt"></i>
                    </a>

                    <div class="navbar-burger"
                         :class="{'is-active': menuActive}"
                         @click="menuActive = !menuActive">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>

                <div class="navbar-menu" :class="{'is-active': menuActive}">
                    <div class="navbar-start">
                        <router-link class="navbar-item" :to="{name: 'backups'}">
                            <span class="icon">
                                <i class="fas fa-archive"></i>
                            </span>
                            <span>Backups</span>
                        </router-link>
                        <router-link class="navbar-item" :to="{name: 'tasks'}">
                            <span class="icon">
                                <i class="fas fa-tasks"></i>
                            </span>
                            <span>Tasks</span>
                        </router-link>
                        <router-link class="navbar-item" :to="{name: 'databases'}">
                            <span class="icon">
                                <i class="fas fa-database"></i>
                            </span>
                            <span>Databases</span>
                        </router-link>
                        <router-link class="navbar-item" :to="{name: 'destinations'}">
                            <span class="icon">
                                <i class="fas fa-cloud"></i>
                            </span>
                            <span>Destinations</span>
                        </router-link>
                        <router-link class="navbar-item" :to="{name: 'users'}">
                            <span class="icon">
                                <i class="fas fa-users"></i>
                            </span>
                            <span>Users</span>
                        </router-link>
                    </div>

                    <div class="navbar-end">
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link is-hidden-mobile">
                                <b-icon size="is-small" pack="far" icon="user" />
                            </a>
                            <div class="navbar-dropdown">
                                <div class="dropdown-item">
                                    <strong>{{ user.name }}</strong>
                                </div>
                                <hr class="dropdown-divider">
                                <router-link class="navbar-item" :to="{name: 'user_profile', params: {id: user.id}}">
                                    <div class="media">
                                        <span class="icon media-left">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <div class="media-content">
                                            <h3>Profile</h3>
                                        </div>
                                    </div>
                                </router-link>
                                <hr class="dropdown-divider">
                                <a class="navbar-item" @click="logout">
                                    <div class="media">
                                    <span class="icon media-left">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </span>
                                        <div class="media-content">
                                            <h3>Logout</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <a title="Settings" class="navbar-item is-hidden-mobile"
                           @click="showSettings = true">
                            <i class="fas fa-cog"></i>
                        </a>
                        <a title="Activities" class="navbar-item is-hidden-mobile"
                           @click="showActivities = true">
                            <i class="far fa-list-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <b-modal has-modal-card :active.sync="showSettings">
            <settings></settings>
        </b-modal>

        <activity-sidebar :active.sync="showActivities" />
    </header>
</template>

<script>
    import ActivitySidebar from "../layout/ActivitySidebar"
    import Settings from "../components/Settings"

    export default {
        components: { ActivitySidebar, Settings },

        data() {
            return {
                menuActive: false,
                showActivities: false,
                showSettings: false,
            }
        },

        watch: {
            $route() {
                this.menuActive = false;
            }
        },

        computed: {
            user() {
                return this.$store.state.user.user;
            },

            appName() {
                return window.APP.name;
            },
        },

        methods: {
            logout() {
                this.axios.post('logout').then(response => {
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                });
            },
        },
    }
</script>