import { Chart, registerables} from 'chart.js';

export default {
    created() {
        Chart.register(...registerables);
    },
    methods: {
        renderChart(container, type, data) {
            new Chart(container, {
                type: type,
                data: data
            });
        }
    }
}
