<template>
    <div class="content has-text-centered">
        <p class="has-text-grey-lighter">
            <slot name="icon"></slot>
        </p>
        <p class="has-text-grey-light">{{ message }}</p>
    </div>
</template>

<script>
    export default {
        name: "NoResults",

        props: {
            message: {
                type: String,
                default: "No data available"
            }
        }
    }
</script>