import { Chart, registerables} from 'chart.js';
import 'chartjs-adapter-moment';

export default {
    data() {
        return {
            _chart: null
        }
    },
    created() {
        Chart.register(...registerables);
    },
    methods: {
        renderChart(container, type, data, options = {}) {
            if(this._chart !== null) {
                this._chart.destroy();
            }
            this._chart = new Chart(container, {
                type: type,
                data: data,
                options: options
            });
        }
    }
}
