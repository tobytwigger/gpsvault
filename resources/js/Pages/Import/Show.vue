<template>
    <div>

        <component
            v-for="(results, type) in groupedImportData"
            :key="type"
            :is="'strava-import-type-' + type in $options.components ? 'strava-import-type-' + type : 'default-import'"
            :results="results"
        >
        </component>

    </div>
</template>

<script>
import {groupBy} from 'lodash';
import DefaultImport from './Results/DefaultImport';
import StravaActivity from './Results/StravaActivity';

export default {
    name: "Show",
    components: {
        DefaultImport,
        'strava-import-type-activities': StravaActivity
    },
    props: {
        importData: {
            required: true,
            type: Object
        }
    },
    computed: {
        groupedImportData() {
            return groupBy(this.importData.import_results, 'type');
        }
    }
}
</script>

<style scoped>

</style>
