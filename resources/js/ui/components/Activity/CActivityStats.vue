<template>
    <v-list two-line dense>
        <v-list-item v-for="summary in statSummary" :key="summary.title">
            <v-list-item-icon>
                <v-tooltip left>
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon
                            v-bind="attrs"
                            v-on="on"
                            color="indigo">
                            {{ summary.icon }}
                        </v-icon>
                    </template>
                    <span>{{ summary.title }}</span>
                </v-tooltip>
            </v-list-item-icon>

            <v-list-item-content v-for="data in summary.data" :key="data.value.unit + data.label">
                <v-list-item-title>{{ data.value.value }} {{ data.value.unit }}</v-list-item-title>
                <v-list-item-subtitle>{{ data.label }}</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action v-if="selectable && !summary.disabled">
                <v-switch :value="selected.indexOf(summary.label) > -1" @change="toggleStatGroup(summary.label, $event)">
                    <v-icon color="grey lighten-1">mdi-chart-line</v-icon>
                </v-switch>
            </v-list-item-action>
        </v-list-item>
        <v-list-item v-if="additionalChartData">
            <v-list-item-icon>
                <v-tooltip left>
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon
                            v-bind="attrs"
                            v-on="on"
                            color="indigo">
                            chart-line
                        </v-icon>
                    </template>
                    <span>Chart data</span>
                </v-tooltip>
            </v-list-item-icon>

            <v-list-item-content>
                <v-list-item-title>Grade</v-list-item-title>
                <v-list-item-subtitle>
                    <v-switch :value="selected.indexOf('grade') > -1" @change="toggleStatGroup('grade', $event)">
                        <v-icon color="grey lighten-1">mdi-chart-line</v-icon>
                    </v-switch>
                </v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-content>
                <v-list-item-title>Battery</v-list-item-title>
                <v-list-item-subtitle>
                    <v-switch :value="selected.indexOf('battery') > -1" @change="toggleStatGroup('battery', $event)">
                        <v-icon color="grey lighten-1">mdi-chart-line</v-icon>
                    </v-switch>
                </v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>
    </v-list>
</template>

<script>
import activityStats from 'ui/mixins/activityStats';
import units from '../../mixins/units';

export default {
    name: "CActivityStats",
    mixins: [activityStats, units],
    props: {
        stats: {
            required: true,
            type: Object
        },
        limit: {
            required: false,
            type: Number,
            default: null
        },
        selectable: {
            required: false,
            type: Boolean,
            default: false
        },
        value: {
            required: false,
            type: Array,
            default: () => []
        },
        additionalChartData: {
            required: false,
            type: Boolean,
            default: false
        },
        multiple: {
            required: false,
            type: Boolean,
            default: false
        }
    },
    methods: {
        toggleStatGroup(label, selected) {
            let opts = this.selected;
            if(selected) {
                if(opts.indexOf(label) === -1) {
                    if(this.multiple) {
                        opts.push(label);
                    } else {
                        opts = [label];
                    }
                }
            } else {
                if(opts.indexOf(label) > -1) {
                    opts.splice(opts.indexOf(label), 1)
                }
            }
            this.selected = opts;
        }
    },
    computed: {
        selected: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val)
            }
        },
        statSummary() {
            return this.allStats.map(stat => {
                stat.data = stat.data.filter(d => d.value !== null);
                return stat;
            }).filter(stat => stat.data.length > 0).slice(0, this.limit ?? this.allStats.length);
        },
        allStats() {
            return [
                {
                    icon: 'mdi-ruler',
                    title: 'Distance',
                    label: 'distance',
                    disabled: true,
                    data: [
                        {value: this.distance, label: 'total'},
                    ]
                },
                {
                    icon: 'mdi-clock',
                    title: 'Time',
                    label: 'time',
                    disabled: true,
                    data: [
                        {value: this.movingTime, label: 'moving'},
                        {value: this.duration, label: 'total'},
                    ]
                },
                {
                    icon: 'mdi-gauge',
                    title: 'Speed',
                    label: 'speed',
                    data: [
                        {value: this.maxSpeed, label: 'max'},
                        {value: this.avgSpeed, label: 'avg'},
                        {value: this.avgPace, label: 'avg'},
                    ]
                },
                {
                    icon: 'mdi-image-filter-hdr',
                    title: 'Elevation',
                    label: 'elevation',
                    data: [
                        {value: this.elevationGain, label: 'gain'},
                        {value: this.minAltitude, label: 'min'},
                        {value: this.maxAltitude, label: 'max'},
                    ]
                },
                {
                    icon: 'mdi-heart',
                    title: 'Heartrate',
                    label: 'heart_rate',
                    data: [
                        {value: this.maxHeartrate, label: 'max'},
                        {value: this.avgHeartrate, label: 'avg'},
                    ]
                },
                {
                    icon: 'mdi-lightning-bolt',
                    title: 'Power',
                    label: 'calories',
                    disabled: true,
                    data: [
                        {value: this.avgWatts, label: 'max'},
                        {value: this.kilojoules, label: 'total'},
                        {value: this.calories, label: 'total'},
                    ]
                },
                {
                    icon: 'mdi-reload',
                    title: 'Cadence',
                    label: 'cadence',
                    data: [
                        {value: this.avgCadence, label: 'avg'},
                    ]
                },
                {
                    icon: 'mdi-thermometer',
                    title: 'Temperature',
                    label: 'temperature',
                    data: [
                        {value: this.averageTemperature, label: 'avg'},
                    ]
                },
            ];
        }
    }
}
</script>

<style scoped>

</style>
