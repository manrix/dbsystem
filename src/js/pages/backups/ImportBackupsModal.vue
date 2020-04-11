<template>
    <form @submit.prevent="importBackups" enctype="multipart/form-data">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Import Backups</p>
            </header>

            <section class="modal-card-body">
                <b-field message="Accepted file extensions: zip, tar, gz">
                    <b-upload
                            v-model="files"
                            accept="zip|tar|gz"
                            multiple
                            drag-drop
                            required>
                        <section class="section">
                            <div class="content has-text-centered">
                                <p>
                                    <b-icon icon="cloud-upload-alt" size="is-large" />
                                </p>
                                <p>Drop your files here or click to upload</p>
                            </div>
                        </section>
                    </b-upload>
                </b-field>

                <table class="table is-fullwidth" v-show="files.length">
                    <thead>
                    <tr>
                        <th>File</th>
                        <th>Type</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(file, index) in files" :key="index">
                        <td>{{ file.name }}</td>
                        <td>
                            <b-field>
                                <b-select v-model="file.backup_type"
                                          placeholder="Select a type"
                                          expanded
                                          required>
                                    <option value="full">Full</option>
                                    <option value="database">Database</option>
                                    <option value="files">Files</option>
                                </b-select>
                            </b-field>
                        </td>
                        <td>
                            <button type="button" title="Remove" class="button is-transparent has-text-danger"
                                    @click="removeFile(index)">
                                <b-icon icon="times" size="is-small" />
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <footer class="modal-card-foot is-justified-center">
                <div class="field is-grouped">
                    <p class="control">
                        <button type="submit" class="button is-link"
                                :disabled="!files.length">
                            <span>Import</span>
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
    import { LOADING } from '../../store/mutation-types'

    export default {
        data() {
            return {
                files: []
            }
        },

        methods: {
            importBackups() {
                this.$store.commit(LOADING, true);

                const formData = new FormData();
                this.files.forEach(function(file, index) {
                    formData.append('files[' + index + ']', file);
                    formData.append('backup_types[' + index + ']', file.backup_type);
                });

                this.axios.post('backups/import', formData).then(() => {
                    this.$emit('imported');
                    this.$parent.close();
                });
            },

            removeFile(index) {
                this.files.splice(index, 1);
            }
        },
    }
</script>

<style scoped>
    .table td {
        vertical-align: middle;
    }
</style>