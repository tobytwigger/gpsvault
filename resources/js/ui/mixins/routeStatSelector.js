import units from './units';

export default {
    mixins: [units],
    data() {
        return {
            dataSource: 'php'
        }
    },
    props: {
        routeModel: {
            required: true,
            type: Object
        }
    },
    computed: {
        hasStats() {
            return this.stats !== null;
        },
        dataSources() {
            return Object.keys(this.routeModel.stats);
        },
        activeDataSource: {
            get() {
                if (this.routeModel.stats.length === 0) {
                    return null;
                }
                if (this.dataSource && this.dataSources.indexOf(this.dataSource) > -1) {
                    return this.dataSource;
                }
                if (Object.keys(this.routeModel.stats).length > 0) {
                    return Object.keys(this.routeModel.stats)[0];
                }
                return null;
            },
            set(val) {
                this.dataSource = val;
            }
        },
        allStats() {
            return Object.values(this.routeModel.stats ?? {});
        },
        stats() {
            if (this.activeDataSource !== null && this.routeModel.stats.hasOwnProperty(this.activeDataSource)) {
                return this.routeModel.stats[this.activeDataSource];
            }
            return null;
        }
    }
}
