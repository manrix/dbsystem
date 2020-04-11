<template>
    <div class="content-wrapper">
        <navigation/>

        <main>
            <router-view></router-view>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="level">
                    <div class="level-left">
                        <div class="level-item">
                            <p>&copy; {{ appName }}</p>
                        </div>
                    </div>
                    <div class="level-right has-text-right">
                        <div class="level-item">
                            <div class="content">
                                <code>v.{{ appVersion }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <b-loading is-full-page :active.sync="isLoading" />
    </div>
</template>

<script>
    import Navigation from "./AppNavigation"

    export default {
        components: { Navigation },

        watch: {
            $route() {
                this.updatePageTitle();
            }
        },

        computed: {
            isLoading() {
                return this.$store.state.loader.isLoading;
            },

            appName() {
                return window.APP.name;
            },

            appVersion() {
                return window.APP.version;
            },
        },

        methods: {
            updatePageTitle() {
                let title = window.APP.name;
                if (this.$route.meta.title) {
                    title += ' - ' + this.$route.meta.title;
                }

                document.title = title;
            }
        },

        created() {
            this.updatePageTitle();
        }
    }
</script>

<style scoped>
    .content-wrapper {
        display: flex;
        height: 0;
        min-height: 100vh;
        flex-direction: column;
    }

    main {
        flex: 1 1 auto;
    }
</style>