<template>
    <div>
        <v-row v-if="hasStats">
            <v-col>
                <c-activity-stats v-model="selectedCharts" :selectable="true" :stats="stats" :additional-chart-data="true"></c-activity-stats>

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

import activityStats from 'ui/mixins/activityStats';
import CActivityStats from './CActivityStats';
import CLineGraph from '../CLineGraph';
import {createDatasets} from './dataset-calc';

export default {
    name: "CActivityAnalysis",
    components: {CLineGraph, CActivityStats},
    mixins: [activityStats],
    props: {
        activity: {
            required: true,
            type: Object
        },
        stats: {
            required: false,
            type: Object,
            default: null
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
        hasStats() {
            return this.stats !== null;
        },
        hasChartData() {
            return this.chartData.datasets.length > 0;
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
            axios.get(this.ziggyRoute('stats.chart', this.stats.id))
                .then(response => {
                    this.loadingChartData = false;
                    this.rawChartData = response.data
                })
                .then(() => this.loadingChartData = false);
        }
    }
}
</script>

<style scoped>

</style>
