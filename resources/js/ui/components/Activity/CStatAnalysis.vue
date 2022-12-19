<template>
    <div>
        <v-row>
            <v-col class="text-center">
                <c-stats :schema="[statSchema]"></c-stats>
            </v-col>
        </v-row>
        <v-row>
            <v-col style="text-align: center;">
                <v-btn-toggle
                    v-show="!loading && chartInformation !== null && !statSchema.disabled"
                    v-model="useDistance"
                    shaped
                    mandatory
                >
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn :value="true" v-bind="attrs"
                                   v-on="on">
                                <span class="hidden-xs-only">Plot against distance</span>

                                <v-icon right>
                                    mdi-ruler
                                </v-icon>
                            </v-btn>
                        </template>
                        <span>Plot '{{statSchema.title}}' from your ride against 'Distance'. This helps on rides with lots of stopping.</span>
                    </v-tooltip>

                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn :value="false" v-bind="attrs"
                                   v-on="on">
                                <span class="hidden-xs-only">Plot against time</span>

                                <v-icon right>
                                    mdi-clock
                                </v-icon>
                            </v-btn>
                        </template>
                        <span>Plot '{{statSchema.title}}' from your ride against 'Time'. This helps to see your actual progress.</span>
                    </v-tooltip>
                </v-btn-toggle>
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
            <v-card
                max-width="600"
                width="600"
                class="mx-auto px-6 my-auto py-6"
                :id="chartContainerId"
                v-show="!loading && chartInformation !== null && !statSchema.disabled"
            >
            </v-card>
        </v-row>
    </div>
</template>

<script>
import CStats from '../CStats';
import CLineGraph from '../CLineGraph';
import units from '../../mixins/units';
import {Chart, registerables} from 'chart.js';
import moment from 'moment';

export default {
    name: "CStatAnalysis",
    components: {CLineGraph, CStats},
    mixins: [units],
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
            loading: false,
            chart: null,
            chartInformation: null,
            mapCanvas: null,
            useDistance: false
        }
    },
    watch: {
        useDistance() {
            this.loadChartData();
        }
    },
    methods: {
        loadChartData() {
            this.loading = true;
            axios.get(route('stats.points', {stats: this.statId, fields: [this.statSchema.pointLabel, this.useDistance ? 'cumulative_distance' : 'time']}))
                .then(response => {
                    if ((response.data?.length ?? 0) > 0) {
                        this.loading = false;
                        this.chartInformation = this.calculateChartInformation(response.data);
                        this.setupChart();
                    }
                })
        },
        setupChart() {
            if (this.chart !== null) {
                this.chart.destroy();
                this.chart = null;
                this.mapCanvas.remove();
            }

            if (this.chartInformation !== null) {
                this.mapCanvas = document.createElement('canvas');
                this.mapCanvas.height = 250;

                document.getElementById(this.chartContainerId).appendChild(this.mapCanvas)

                this.chart = new Chart(this.mapCanvas.getContext('2d'), {
                    type: 'line',
                    ...this.chartInformation
                });
            }
        },
        calculateChartInformation(activityPoints) {
            if(activityPoints === null) {
                return {
                    data: {
                        labels: [],
                        datasets: []
                    }
                }
            }
            const data = [];
            const labels = [];

            activityPoints.forEach(p => {
                if (this.useDistance === false && p.time) {
                    labels.push(moment(p.time).unix() * 1000);
                    data.push(p[this.statSchema.pointLabel]);
                }
                if (this.useDistance === true && p.cumulative_distance) {
                    labels.push(this.convert(p.cumulative_distance, 'distance').value);
                    data.push(p[this.statSchema.pointLabel]);
                }
            });

            return {
                options: {
                    events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                    scales: {
                        x: this.useDistance ? {type: 'linear'} : {
                            type: 'time',
                            time: {
                                unit: 'minute'
                            }
                        },
                        y: {type: 'linear', beginAtZero: true},
                    },
                    plugins: {
                        title: {
                            align: "center",
                            display: true,
                            text: (this.useDistance
                                    ? "Distance (" + this.getUserUnit('distance') + ") / "
                                    : "Time / ")
                            + this.statSchema.title + " (" + this.getUserUnit(this.statSchema.label) + ")",
                        },
                        legend: {display: false},
                        tooltip: {
                            displayColors: false,
                            callbacks: {
                                title: (tooltipItems) => {
                                    this.$emit('update:selected', tooltipItems[0].dataIndex)
                                    if(this.useDistance) {
                                        return "Distance: " + tooltipItems[0].label + this.getUserUnit('distance');
                                    }
                                    return "Time: " + tooltipItems[0].label;
                                },
                                label: (tooltipItem) => {
                                    return this.statSchema.title + " : " + tooltipItem.raw + this.getUserUnit('elevation');
                                },
                            }
                        }
                    },
                    animation: false,
                    maintainAspectRatio: false,
                    interaction: {intersect: false, mode: 'index'},
                    tooltip: {position: 'nearest'},
                },
                data: {
                    labels: labels,
                    datasets: [
                        {
                            fill: true,
                            borderColor: this.statSchema.lineColour ?? '#66ccff',
                            backgroundColor: (this.statSchema.lineColour ?? '#66ccff') + 'cc',
                            tension: 0.1,
                            pointRadius: 0,
                            spanGaps: true,
                            data: data
                        }
                    ]
                }
            };
        }
    },
    computed: {
        chartContainerId() {
            return this.statSchema.label + '-chart-container-stat-analysis';
        }
    },
    created() {
        Chart.register(...registerables);
    },

    mounted() {
        this.loadChartData();
    }
}
</script>

<style scoped>

</style>
