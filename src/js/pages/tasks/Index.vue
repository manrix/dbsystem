<template>
    <base-layout>
        <template slot="pageToolsRight">
            <b-field>
                <router-link
                        :to="{ name: 'new_task'}"
                        class="button is-link">
                    <b-icon size="is-small" icon="plus"></b-icon>
                    <span>New Task</span>
                </router-link>
            </b-field>
        </template>

        <tasks></tasks>
    </base-layout>
</template>

<script>
    import Tasks from '../../components/tasks/Tasks'

    export default {
        components: {Tasks}
    }
</script>