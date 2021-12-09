<template>
    <div>
        <button @click="createChart">CREATE THE CHART</button>
        <canvas ref="chartContainer" width="600" height="300"></canvas>
    </div>
</template>

<script>
import { Chart, registerables} from 'chart.js';
import 'chartjs-adapter-moment';
import { v4 as uuidv4 } from 'uuid';

export default {
    name: "Chart",
    props: {
        type: {
            required: true,
            type: String
        },
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
            chartKey: uuidv4()
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
            // if(this.chart !== null) {
            //     this.chart.destroy();
            // }
            // this.chartKey = uuidv4();
            this.$nextTick(() => this.chart = new Chart(this.$refs.chartContainer.getContext('2d'), this.chartConfig));
        }
    },
    computed: {
        chartConfig() {
            return {
                type: this.type,
                data: this.data,
                options: this.options
            };
        }
    }
}
</script>

<style scoped>

</style>
