<template>
    <div>
        <div v-if="stats.is_power_data_available">
            <data-display-tile title="Calories" :value="stats.calories?.toString()?.toString()">
            </data-display-tile>
            <data-display-tile title="Kilojoules" :value="stats.kilojoules?.toString()">
            </data-display-tile>
            <data-display-tile title="Average Watts" :value="stats.average_watts?.toString()">
            </data-display-tile>
        </div>
        <div v-if="stats.is_heartrate_data_available">
            <data-display-tile title="Max Heartrate" :value="stats.max_heartrate?.toString()">
            </data-display-tile>
            <data-display-tile title="Average Heartrate" :value="stats.average_heartrate?.toString()">
            </data-display-tile>
        </div>
        <div v-if="stats.is_position_data_available">
            <data-display-tile title="Distance" :value="$converter(stats.distance, 'm/s')">
            </data-display-tile>
            <data-display-tile title="Start" :value="stats.start_latitude + ', ' + stats.start_longitude?.toString()">
            </data-display-tile>
            <data-display-tile title="End" :value="stats.start_latitude + ', ' + stats.start_longitude?.toString()">
            </data-display-tile>
        </div>
        <div v-if="stats.is_temperature_data_available">
            <data-display-tile title="Average Temperature" :value="$converter(stats.average_temp, 'C')">
            </data-display-tile>
        </div>
        <div v-if="stats.is_cadence_data_available">
            <data-display-tile title="Average Cadence" :value="stats.average_cadence?.toString()">
            </data-display-tile>
        </div>
        <div v-if="stats.is_speed_data_available">
            <data-display-tile title="Max Speed" :value="$converter(stats.max_speed, 'm/s')">
            </data-display-tile>
            <data-display-tile title="Average Speed" :value="$converter(stats.average_speed, 'm/s')">
            </data-display-tile>
            <data-display-tile title="Average Pace" :value="$converter(stats.average_page, 's/m')">
            </data-display-tile>
        </div>
        <div v-if="stats.is_time_data_available">
            <data-display-tile title="Moving Time" :value="$converter(stats.moving_time, 's')">
            </data-display-tile>
            <data-display-tile title="Total Time" :value="$converter(stats.duration, 's')">
            </data-display-tile>
            <data-display-tile title="Started At" :value="stats.started_at?.toString()">
            </data-display-tile>
            <data-display-tile title="Finished At" :value="stats.finished_at?.toString()">
            </data-display-tile>
        </div>
        <div v-if="stats.is_elevation_data_available">
            <data-display-tile title="Elevation Loss" :value="$converter(stats.elevation_loss, 'm')">
            </data-display-tile>
            <data-display-tile title="Elevation Gain" :value="$converter(stats.elevation_gain, 'm')">
            </data-display-tile>
            <data-display-tile title="Max Altitude" :value="$converter(stats.min_altitude, 'm')">
            </data-display-tile>
            <data-display-tile title="Min Altitude" :value="$converter(stats.max_altitude, 'm')">
            </data-display-tile>
        </div>

    </div>
</template>

<script>
import DataDisplayTile from './DataDisplayTile';
import moment from 'moment';

export default {
    name: "Stats",
    components: {
        DataDisplayTile
    },
    props: {
        stats: {
            required: true,
            type: Object
        }
    },
    methods: {
        convertTime(time) {
            return this.runConversion(time, (time) => moment(time).format('DD/MM/YYYY HH:mm:ss'))
        },
        convertDistance(distance) {
            return this.runConversion(distance, (distance) => (Math.round(((distance / 1000) + Number.EPSILON) * 100) / 100) + 'km')
        },
        convertDuration(duration) {
            return this.runConversion(duration, (duration) => moment.utc(duration*1000).format('HH:mm:ss'))
        },
        convertElevation(elevation) {
            return this.runConversion(elevation, (elevation) => Math.round(elevation) + 'm')
        },
        convertSpeed(speed) {
            return this.runConversion(speed, (speed) => Math.round((speed * 3.6) * 100)/100 + 'km/h')
        },
        convertPace(pace) {
            return this.runConversion(pace, (pace) => Math.round((pace / 60) * 100)/100 + 'mins/km')
        },
        runConversion(value, convert) {
            return value === null ? 'N/A' : convert(value);
        }
    }
}
</script>

<style scoped>

</style>
