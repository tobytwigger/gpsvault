<template>
    <div>
        <v-row>
            <v-col style="margin: 10px;">
                <v-card
                    max-width="600"
                    width="600"
                    class="mx-auto"
                    id="elevation-canvas-container"
                >
                </v-card>
                <div v-if="result.coordinates.length === 0">
                    No route
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>

import {cloneDeep} from 'lodash';
import draggable from 'vuedraggable'
import {Chart, registerables} from 'chart.js';
import units from '../../../../mixins/units';

export default {
    name: "CElevationControl",
    mixins: [units],
    components: {
        draggable
    },
    props: {
        result: {
            required: false,
            default: null
        }
    },
    data() {
        return {
            chart: null,
            elevationCanvas: null
        }
    },
    watch: {
        result: {
            deep: true,
            handler: function(val) {
                this.setupChart();
            }
        }
    },
    computed: {
        elevationProfileDataset() {
            const data = [];
            const labels = [];
            this.result.coordinates.forEach((coords, index) => {
                labels.push(this.convert(coords[3], 'distance').value);
                data.push(coords[2]);
            });

            return {data: data, labels: labels};
            // return data.filter((d, i) => i % this.graphScaleFactor === 0);
        }
    },
    methods: {
        setupChart() {
            if(this.chart !== null) {
                this.chart.destroy();
                this.chart = null;
                this.elevationCanvas.remove();
            }

            if(this.result.coordinates.length > 0) {
                this.elevationCanvas = document.createElement('canvas');
                this.elevationCanvas.height = 250;

                document.getElementById('elevation-canvas-container').appendChild(this.elevationCanvas)

                this.chart = new Chart(this.elevationCanvas.getContext('2d'), {
                    type: 'line',
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        animation: false,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        tooltip: { position: 'nearest' },
                        scales: {
                            x: { type: 'linear' },
                            y: { type: 'linear', beginAtZero: true },
                            y1: { type: 'linear', display: true, position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }},
                        },
                        // plugins: {
                        //     title: { align: "end", display: true, text: "Distance, m / Elevation, m" },
                        //     legend: { display: false },
                        //     tooltip: {
                        //         displayColors: false,
                        //         callbacks: {
                        //             title: (tooltipItems) => {
                        //                 return "Distance: " + tooltipItems[0].label + 'm'
                        //             },
                        //             label: (tooltipItem) => {
                        //                 return "Elevation: " + tooltipItem.raw + 'm'
                        //             },
                        //         }
                        //     }
                        // }
                    },
                    data: {
                        labels: this.elevationProfileDataset.labels,
                        datasets: [
                            {
                                fill: true,
                                borderColor: '#66ccff',
                                backgroundColor: '#66ccff66',
                                tension: 0.1,
                                pointRadius: 0,
                                spanGaps: true,
                                data: this.elevationProfileDataset.data
                            }
                        ]
                    }
                });
            }
        }
    },
    mounted() {
        this.setupChart();
    },
    created() {
        Chart.register(...registerables);
    },

}
</script>

<style scoped>
</style>
