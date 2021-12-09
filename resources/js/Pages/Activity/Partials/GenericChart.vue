<template>
    <div v-if="loadingChartData">
        Loading chart data
    </div>
    <div v-else-if="!hasChartData">
        Chart not available
    </div>
    <chart type="line" :options="chartOptions" :data="chartData" v-if="chartData.datasets.length > 0">

    </chart>
</template>

<script>

import Chart from './Charts/Chart';
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
                this.chartData = this.calculateChartData(rawData);
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
        },
        calculateChartData() {
            let availableDatasets = {
                elevation: {
                    data: this.rawData.map(p =>  {
                        return {x: p.time * 1000, y: p.elevation};
                    }),
                    label: 'Elevation (m)',
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                cadence: {
                    data: this.rawData.map(p =>  {
                        return {x: p.time * 1000, y: p.cadence};
                    }),
                    label: 'Cadence (rpm)',
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                temperature: {
                    data: this.rawData.map(p =>  {
                        return {x: p.time * 1000, y: p.temperature};
                    }),
                    label: 'Temperature (deg C)',
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                heart_rate: {
                    data: this.rawData.map(p =>  {
                        return {x: p.time * 1000, y: p.heart_rate};
                    }),
                    label: 'Heart Rate (bpm)',
                    fill: false,
                    borderColor: 'rgb(255, 0, 0)',
                    tension: 0.1
                },
                speed: {
                    data: this.rawData.map(p =>  {
                        return {x: p.time * 1000, y: p.speed};
                    }),
                    label: 'Speed (km/h)',
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                grade: {
                    data: this.rawData.map(p =>  {
                        return {x: p.time * 1000, y: p.grade};
                    }),
                    label: 'Grade (%)',
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                battery: {
                    data: this.rawData.map(p =>  {
                        return {x: p.time * 1000, y: p.battery};
                    }),
                    label: 'Battery (%)',
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                calories: {
                    data: this.rawData.map(p =>  {
                        return {x: p.time * 1000, y: p.calories};
                    }),
                    label: 'Calories',
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }
            }
            let datasets = [];
            this.availableChartData.forEach(property => {
                if(availableDatasets.hasOwnProperty(property)) {
                    datasets.push(availableDatasets[property]);
                }
            });
            return {datasets: datasets};
        }
    },
    computed: {
        hasChartData() {
            return this.rawData.length > 0;
        },
        availableChartData() {
            return ['elevation', 'cadence', 'temperature', 'heart_rate', 'speed', 'grade', 'battery', 'calories']
                .filter((property) => this.rawData.filter(d => d.hasOwnProperty(property) && d[property] !== null).length > 0)
        }
    }
}
</script>

<style scoped>

</style>
