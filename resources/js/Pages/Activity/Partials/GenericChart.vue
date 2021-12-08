<template>
    <div v-if="loadingChartData">
        Loading chart data
    </div>
    <div v-else-if="!hasChartData">
        Chart not available
    </div>
    <chart type="line" :options="chartOptions" :data="chartData">

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
                spanGaps: true,
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
            console.log('about to calculate')
            let result = {
                datasets: [
                    {
                        data: this.rawData.map(p =>  {
                            return {x: p.time, y: p.elevation};
                        }),
                        label: 'Elevation (m)',
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }
                ]
            };
            console.log('calculated');
            return result;
        }
    },
    computed: {
        hasChartData() {
            return this.rawData.length > 0;
        },
        availableChartData() {
            return ['elevation'];
            return ['elevation', 'cadence', 'temperature', 'heart_rate', 'speed', 'grade', 'battery', 'calories']
                .filter((property) => this.rawData.filter(d => d.hasOwnProperty(property) && d[property] !== null).length > 0)
        }
    }
}
</script>

<style scoped>

</style>
