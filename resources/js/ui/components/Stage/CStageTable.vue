<template>
    <v-data-table
        :headers="headers"
        :single-expand="true"
        :expanded.sync="expanded"
        show-expand
        :items="stages"
        :items-per-page="5"
        class="elevation-1"
    >
        <template v-slot:expanded-item="{ headers, item }">
            <td :colspan="headers.length">
                <p>Description: {{item.description}}</p>

                <p>Route information (distance etc)</p>

                <p>{{ item.date }}</p>

                {{ item }}
            </td>
        </template>

    </v-data-table>
</template>

<script>
export default {
    name: "CStageTable",
    props: {
        stages: {
            required: false,
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            headers: [
                {text: 'Stage', value: 'stage_number'},
                {text: 'Name', value: 'name'}
            ],
            expanded: []
        }
    }
}
</script>

<style scoped>

</style>
