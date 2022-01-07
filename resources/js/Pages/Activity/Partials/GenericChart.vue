<template>
    <div>
        <div v-if="loadingChartData">
            Loading chart data
        </div>
        <div v-else-if="!hasChartData">
            Chart not available
        </div>
        <chart v-if="rawData.length > 0 && chartData.datasets.length > 0" :key="stats.integration" :data="chartData"
               :options="chartOptions" type="line">

        </chart>
    </div>
</template>

<script>

import Chart from './Charts/Chart';
import {createDatasets} from './dataset-calc';

export default {
    name: "GenericChart",
    components: {Chart},
    props: {
        stats: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            rawData: [],
            loadingChartData: false,
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
    watch: {
        rawData: {
            handler(rawData) {
                createDatasets(this.rawData)
                    .then(result => this.chartData = result);
            },
            deep: true
        },
        stats: {
            handler() {
                this.rawData = [];
                this.loadRawChartData();
            },
            deep: true
        }
    },
    mounted() {
        this.loadRawChartData();
    },
    methods: {
        loadRawChartData() {
            this.loadingChartData = true;
            axios.get(route('stats.chart', this.stats.id))
                .then(response => {
                    this.loadingChartData = false;
                    this.rawData = response.data
                })
                .then(() => this.loadingChartData = false);
        }
    },
    computed: {
        hasChartData() {
            return this.rawData.length > 0;
        }
    }
}
</script>

<style scoped>

</style>
