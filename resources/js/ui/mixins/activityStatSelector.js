import units from './units';

export default {
    mixins: [units],
    data() {
        return {
            dataSource: 'php'
        }
    },
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    computed: {
        hasStats() {
            return this.stats !== null;
        },
        dataSources() {
            return Object.keys(this.activity.stats);
        },
        activeDataSource: {
            get() {
                if (this.activity.stats.length === 0) {
                    return null;
                }
                if (this.dataSource && this.dataSources.indexOf(this.dataSource) > -1) {
                    return this.dataSource;
                }
                if (Object.keys(this.activity.stats).length > 0) {
                    return Object.keys(this.activity.stats)[0];
                }
                return null;
            },
            set(val) {
                this.dataSource = val;
            }
        },
        allStats() {
            return Object.values(this.activity.stats ?? {});
        },
        stats() {
            if (this.activeDataSource !== null && this.activity.stats.hasOwnProperty(this.activeDataSource)) {
                return this.activity.stats[this.activeDataSource];
            }
            return null;
        }
    }
}
