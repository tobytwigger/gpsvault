<template>
    <div>
        <v-row>
            <v-col class="text-center">
                <c-stats :schema="[statSchema]"></c-stats>
            </v-col>
        </v-row>
        <v-row no-gutters v-if="!statSchema.disabled">
            <v-col>
                <hr/>
            </v-col>
        </v-row>
        <v-row>
            <v-col v-if="loading">
                Loading data
            </v-col>
            <v-col v-else-if="!statSchema.disabled">
                <c-line-graph
                    :key="'graph-' + statSchema.label"
                    v-if="chartData.datasets.length > 0"
                    :data="chartData"
                    :options="chartOptions"
                ></c-line-graph>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import CStats from '../CStats';
import CLineGraph from '../CLineGraph';

export default {
    name: "CStatAnalysis",
    components: {CLineGraph, CStats},
    props: {
        statSchema: {
            required: true,
            type: Object
        },
        activity: {
            required: true,
            type: Object
        },
        statId: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            chartData: {datasets: []},
            loading: false,
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
    methods: {
        loadChartData() {
            this.loading = true;
            axios.get(route('stats.points', {stats: this.statId, fields: [this.statSchema.pointLabel, 'time']}))
                .then(response => {
                    if((response.data?.length ?? 0)> 0) {
                        this.loading = false;
                        this.chartData.datasets[0] = this.convertToDataset(response.data);
                    }
                })
        },
        convertToDataset(activityPoints) {
            let dataset = {
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            };
            dataset.data = activityPoints.map(p => {
                return {x: p.time, y: p[this.statSchema.pointLabel]}
            })
            return dataset;
        },
        parseChartData() {
            // this.parsingChartData = true;
            // if(this.points.length > 0) {
            //     createDatasets(this.points, this.statSchema.label)
            //         .then(result => {
            //             this.chartData = result;
            //             this.parsingChartData = false;
            //         });
            // }
        },
    },
    mounted() {
        this.loadChartData();
        // this.parseChartData();
    },
    watch: {
        points: {
            deep: true,
            handler: function() {
                // this.parseChartData();
            }
        }
    }
}
</script>

<style scoped>

</style>
