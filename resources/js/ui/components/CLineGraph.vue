<template>
    <div>
        <canvas ref="chartContainer" width="600" height="300" ></canvas>
    </div>
</template>

<script>
import { Chart, registerables} from 'chart.js';
import 'chartjs-adapter-moment';

export default {
    name: "CLineGraph",
    props: {
        data: {
            required: true,
            type: Object
        },
        options: {
            required: false,
            type: Object,
            default: () => {
                return {}
            }
        },
    },
    data() {
        return {
            chart: null,
            mutableChartData: {},
        }
    },
    created() {
        Chart.register(...registerables);
    },
    mounted() {
        this.createChart();
    },
    methods: {
        createChart() {
            if(this.chart !== null) {
                this.chart.destroy();
            }
            this.mutableChartData = this.data;
            this.$nextTick(() => this.chart = new Chart(this.$refs.chartContainer.getContext('2d'), {
                data: this.mutableChartData,
                type: 'line',
                options: this.options
            }));
        }
    }
}
</script>

<style scoped>

</style>
