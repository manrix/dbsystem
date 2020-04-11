<template>
    <base-layout>
        <template slot="pageToolsRight">
            <b-field>
                <button type="button" class="button is-link" @click="importBackup()">
                    <b-icon icon="cloud-upload-alt" size="is-small"></b-icon>
                    <span>Import</span>
                </button>
            </b-field>
        </template>

        <backups ref="backupsList" :task_id="task_id" :user_id="user_id"></backups>
    </base-layout>
</template>

<script>
    import Backups from '../../components/backups/Backups'
    import ImportBackups from './ImportBackupsModal'

    export default {
        components: {Backups},

        data() {
            return {
                task_id: null,
                user_id: null,
            }
        },

        watch: {
            '$route.query.task': function (task) {
                this.task_id = task;
            },

            '$route.query.user': function (user) {
                this.user_id = user;
            },
        },

        methods: {
            importBackup() {
                this.$modal.open({
                    parent: this,
                    component: ImportBackups,
                    hasModalCard: true,
                    events: {
                        imported: () => {
                            this.$refs.backupsList.getData();
                        }
                    }
                });
            },
        },

        created() {
            this.task_id = this.$route.query.task || null;
            this.user_id = this.$route.query.user || null;
        }
    }
</script>