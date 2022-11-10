<template>
    <div>
        <v-row v-if="hasStats">
            <v-col>
                <c-stats v-model="selectedCharts" :selectable="true" :schema="processedSchema" data-hint="You can toggle the data below to show it in the graph." data-scroll-to="element">
                    <template v-slot:append="{selected, toggleStatGroup}">
                        <v-list-item>
                            <v-list-item-icon>
                                <v-tooltip left>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-icon
                                            v-bind="attrs"
                                            v-on="on"
                                            color="indigo">
                                            chart-line
                                        </v-icon>
                                    </template>
                                    <span>Chart data</span>
                                </v-tooltip>
                            </v-list-item-icon>

                            <v-list-item-content>
                                <v-list-item-title>Grade</v-list-item-title>
                                <v-list-item-subtitle>
                                    <v-switch :value="selected.indexOf('grade') > -1" @change="toggleStatGroup('grade', $event)">
                                        <v-icon color="grey lighten-1">mdi-chart-line</v-icon>
                                    </v-switch>
                                </v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-content>
                                <v-list-item-title>Battery</v-list-item-title>
                                <v-list-item-subtitle>
                                    <v-switch :value="selected.indexOf('battery') > -1" @change="toggleStatGroup('battery', $event)">
                                        <v-icon color="grey lighten-1">mdi-chart-line</v-icon>
                                    </v-switch>
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </c-stats>

            </v-col>
            <v-col>
                <c-line-graph
                    :key="chartKey"
                    v-if="chartData.datasets.length > 0"
                    :data="chartData"
                    :options="chartOptions"
                ></c-line-graph>
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
import {createDatasets} from './dataset-calc';
import CStats from '../CStats';

export default {
    name: "CActivityAnalysis",
    components: {CStats, CLineGraph},
    mixins: [stats],
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            selectedCharts: [],
            chartKey: 'none-selected',
            loadingChartData: false,
            rawChartData: [],
            parsingChartData: false,
            chartData: {datasets: []},
            chartOptions: {
                parsing: false,
                normalized: true,
                spanGaps: true,
                minRotation: 0,
                maxRotation: 0,
                animation: false,
                plugins: {
                    decimation: {
                        enabled: true,
                        algorithm: 'min-max'
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute'
                        }
                    }
                }
            }
        }
    },
    mounted() {
        this.loadRawChartData();
    },
    computed: {
        allStats() {
            return this.activity.stats;
        },
        processedSchema() {
            // let missingItems = {};
            // for(let activityPointIndex in this.rawChartData) {
            //     if(activityPointIndex > 5) {
            //         continue;
            //     }
            //     console.log(this.rawChartData[activityPointIndex]);
            //     for(let key in this.rawChartData[activityPointIndex]) {
            //         if(this.rawChartData[activityPointIndex][key] === null) {
            //             missingItems[key] = (missingItems[key] ?? 0) + 1;
            //         }
            //     }
            //     console.log(missingItems);
            // }
            // // console.log(missingItems);
            // this.statSchema.map(s => {
            //     // console.log(missingItems[s.label]);
            //     if(missingItems.hasOwnProperty(s.label) && missingItems[s.label] < 2 && 0 < 0.05) {
            //         s.disabled = false;
            //         // console.log((missingItems[s.label]));
            //     }
            //     return s;
            // });
            return this.statSchema;
        }
    },
    watch: {
        parsingChartData(val) {
            if(val === false) {
                this.$nextTick(() => this.chartKey = (this.selectedCharts.length > 0 ? this.selectedCharts.join(',') : 'none-selected'));
            }
        },
        selectedCharts: {
            handler() {
                this.parseChartData();
            },
            deep: true
        },
        rawChartData: {
            handler() {
                this.parseChartData()
            },
            deep: true
        },
        stats: {
            handler() {
                this.rawChartData = [];
                this.loadRawChartData();
            },
            deep: true
        }
    },
    methods: {
        parseChartData() {
            this.parsingChartData = true;
            createDatasets(this.rawChartData, this.selectedCharts)
                .then(result => {
                    this.chartData = result;
                    this.parsingChartData = false;
                });
        },
        loadRawChartData() {
            this.loadingChartData = true;
            if(this.stats) {
                axios.get(route('stats.points', this.stats.id))
                    .then(response => {
                        this.loadingChartData = false;
                        this.rawChartData = response.data
                    })
                    .then(() => this.loadingChartData = false);
            }
        }
    }
}
</script>

<style scoped>

</style>
