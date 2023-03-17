<template>
    <div>
        <v-row v-if="hasStats">
            <v-col>
                <v-tabs
                    vertical
                    v-model="selectedTab"
                >
                    <v-tab :href="'#tab-' + s.label" v-for="s of statSchema" :key="'tab-activator-' + s.label">
                        <v-icon left>{{ s.icon }}</v-icon>
                        {{ s.title }}
                    </v-tab>

                    <v-tab-item :value="'tab-' + s.label" v-for="s of statSchema" :key="'tab-content-' + s.label">
                        <c-stat-analysis
                            v-if="'tab-' + s.label === selectedTab" :stat-id="stats?.id" :stat-schema="s" :activity="activity">

                        </c-stat-analysis>
                    </v-tab-item>
                </v-tabs>

            </v-col>
            <v-col>

            </v-col>
        </v-row>
        <v-row v-else>
            <v-col>
                No stats
            </v-col>
        </v-row>
    </div>
</template>

<script>

import stats from 'ui/mixins/stats';
import CLineGraph from '../CLineGraph';
// import {createDatasets} from './dataset-calc';
import CStatAnalysis from './CStatAnalysis';

export default {
    name: "CActivityAnalysis",
    components: {CStatAnalysis, CLineGraph},
    mixins: [stats],
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            selectedTab: null,
        }
    },
    computed: {
        allStats() {
            return this.activity.stats;
        },
    }
}
</script>

<style scoped>

</style>
