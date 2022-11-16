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
                <div v-if="coordinates.length === 0">
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
        coordinates: {
            required: false,
            type: Array,
            default: () => []
        },
        selected: {
            required: false,
            type: Number,
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
        coordinates: {
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
            this.coordinates.forEach((coords, index) => {
                labels.push(this.convert(coords[3], 'distance').value);
                data.push(this.convert(coords[2], 'elevation').value);
            });

            return {data: data, labels: labels};
        }
    },
    methods: {
        setupChart() {
            if(this.chart !== null) {
                this.chart.destroy();
                this.chart = null;
                this.elevationCanvas.remove();
            }

            if(this.coordinates.length > 0) {
                this.elevationCanvas = document.createElement('canvas');
                this.elevationCanvas.height = 250;

                document.getElementById('elevation-canvas-container').appendChild(this.elevationCanvas)

                this.chart = new Chart(this.elevationCanvas.getContext('2d'), {
                    type: 'line',
                    options: {
                        events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                        scales: {
                            x: { type: 'linear' },
                            y: { type: 'linear', beginAtZero: true },
                        },
                        plugins: {
                            clearSelected: {},
                            title: { align: "center", display: true, text: "Distance (" + this.getUserUnit('distance') + ") / Elevation (" + this.getUserUnit('elevation') + ")" },
                            legend: { display: false },
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
                        interaction: { intersect: false, mode: 'index' },
                        tooltip: { position: 'nearest' },
                    },
                    plugins: [
                        {
                            id: 'clearSelected',
                            afterEvent: (chart, args, pluginOptions) => {
                                if (args.event.type === 'mouseout') {
                                    this.$emit('update:selected', null);
                                }
                            },
                        }
                    ],
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
