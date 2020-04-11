<template>
    <div>
        <section class="hero is-light page-tools has-text-centered-mobile">
            <div class="hero-body">
                <div class="container">
                    <div class="columns is-vcentered">
                        <div class="column is-half-desktop">
                            <transition appear name="fade">
                                <div :key="$route.meta.title">
                                    <p class="title">
                                        {{ $route.meta.title }}
                                    </p>
                                    <p class="subtitle">
                                        <breadcrumbs/>
                                    </p>
                                </div>
                            </transition>
                        </div>
                        <div class="column has-text-right-desktop">
                            <slot name="pageToolsRight"></slot>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="page-content" class="section">
            <div class="container">
                <slot></slot>
            </div>
        </section>
    </div>
</template>

<script>
    export default {
        name: "BaseLayout"
    }
</script>

<style scoped>
    .section {
        padding-top: 1.5rem;
    }
</style>