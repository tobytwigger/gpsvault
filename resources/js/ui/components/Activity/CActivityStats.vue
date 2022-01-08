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
        limit: {
            required: false,
            type: Number,
            default: null
        }
    },
    computed: {
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
                    data: [
                        {value: this.distance, label: 'total'},
                    ]
                },
                {
                    icon: 'mdi-clock',
                    title: 'Time',
                    data: [
                        {value: this.movingTime, label: 'moving'},
                        {value: this.duration, label: 'total'},
                    ]
                },
                {
                    icon: 'mdi-gauge',
                    title: 'Speed',
                    data: [
                        {value: this.maxSpeed, label: 'max'},
                        {value: this.avgSpeed, label: 'avg'},
                        {value: this.avgPace, label: 'avg'},
                    ]
                },
                {
                    icon: 'mdi-image-filter-hdr',
                    title: 'Elevation',
                    data: [
                        {value: this.elevationGain, label: 'gain'},
                        {value: this.minAltitude, label: 'min'},
                        {value: this.maxAltitude, label: 'max'},
                    ]
                },
                {
                    icon: 'mdi-heart',
                    title: 'Heartrate',
                    data: [
                        {value: this.maxHeartrate, label: 'max'},
                        {value: this.avgHeartrate, label: 'avg'},
                    ]
                },
                {
                    icon: 'mdi-lightning-bolt',
                    title: 'Power',
                    data: [
                        {value: this.avgWatts, label: 'max'},
                        {value: this.kilojoules, label: 'total'},
                        {value: this.calories, label: 'total'},
                    ]
                },
                {
                    icon: 'mdi-reload',
                    title: 'Cadence',
                    data: [
                        {value: this.avgCadence, label: 'avg'},
                    ]
                },
                {
                    icon: 'mdi-thermometer',
                    title: 'Temperature',
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
