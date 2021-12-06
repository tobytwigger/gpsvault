<template>
    <canvas ref="chartContainer" width="600" height="300"></canvas>
</template>

<script>
import ChartMixin from './chart';

export default {
    name: "Heartrate",
    mixins: [ChartMixin],
    props: {
        stats: {
            type: Object,
            required: true
        }
    },
    mounted() {
        this.renderChart(this.$refs.chartContainer.getContext('2d'), 'line', this.chartData);
    },
    computed: {
        chartData() {
            return {
                datasets: [
                    {
                        data: this.stats.points.map(p =>  {
                            return {x: p.time, y: p.heartRate};
                        }),
                        label: 'Heart Rate (bpm)',
                        fill: false,
                        borderColor: 'rgb(255, 0, 0)',
                        tension: 0.1
                    }
                ]
            }
        }
    }
}
</script>

<style scoped>

</style>
