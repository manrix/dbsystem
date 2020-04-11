<template>
    <resources-table
            ref="table"
            :api="api"
            :search="false"
            :checkable="false"
            :detailed="true"
    >
        <template slot="table-fields" slot-scope="props">
            <b-table-column field="uid" label="Execution ID">
                {{ props.row.uid }}
            </b-table-column>

            <b-table-column field="created_at" label="Registered" sortable>
                {{ formatToLocalDateTime(props.row.created_at) }}
            </b-table-column>
        </template>

        <template slot="detail" slot-scope="props">
            <div class="content is-small">
                <ul style="margin-top: 0;">
                    <li v-for="log in props.row.logs">
                        {{ log.created_at }} - {{ log.message }}
                    </li>
                </ul>
            </div>
        </template>
    </resources-table>
</template>

<script>
    import ResourcesTable from '../../components/ResourcesTable'

    export default {
        name: "Logs",

        components: {ResourcesTable},

        props: {
            task_id: {
                required: true
            },
            api: {
                type: String,
                required: true
            }
        }
    }
</script>