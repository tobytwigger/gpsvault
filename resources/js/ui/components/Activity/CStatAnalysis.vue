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
            <v-card
                max-width="600"
                width="600"
                class="mx-auto"
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
            mapCanvas: null
        }
    },
    methods: {
        loadChartData() {
            this.loading = true;
            axios.get(route('stats.points', {stats: this.statId, fields: [this.statSchema.pointLabel, 'time']}))
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
                if (p.time) {
                    labels.push(moment(p.time).unix() * 1000);
                    data.push(p[this.statSchema.pointLabel]);
                }
            });
            console.log(labels);

            return {
                options: {
                    events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                    scales: {
                        x: {
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
                            text: "Distance (" + this.getUserUnit('distance') + ") / Elevation (" + this.getUserUnit('elevation') + ")"
                        },
                        legend: {display: false},
                        tooltip: {
                            displayColors: false,
                            callbacks: {
                                title: (tooltipItems) => {
                                    this.$emit('update:selected', tooltipItems[0].dataIndex)
                                    return "Distance: " + tooltipItems[0].label + this.getUserUnit('distance')
                                },
                                label: (tooltipItem) => {
                                    return "Elevation: " + tooltipItem.raw + this.getUserUnit('elevation')
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
                            backgroundColor: '#66ccffcc',
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
