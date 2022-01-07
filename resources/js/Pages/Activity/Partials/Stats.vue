<template>
    <div>
        <div v-if="stats.is_power_data_available">
            <data-display-tile title="Calories" :value="optional(stats.calories.toString())">
            </data-display-tile>
            <data-display-tile title="Kilojoules" :value="optional(stats.kilojoules.toString())">
            </data-display-tile>
            <data-display-tile title="Average Watts" :value="optional(stats.average_watts.toString())">
            </data-display-tile>
        </div>
        <div v-if="stats.is_heartrate_data_available">
            <data-display-tile title="Max Heartrate" :value="optional(stats.max_heartrate.toString())">
            </data-display-tile>
            <data-display-tile title="Average Heartrate" :value="optional(stats.average_heartrate.toString())">
            </data-display-tile>
        </div>
        <div v-if="stats.is_position_data_available">
            <data-display-tile title="Distance" :value="$converter(stats.distance, 'm')">
            </data-display-tile>
            <data-display-tile title="Start" :value="optional(stats.start_latitude) + ', ' + optional(stats.start_longitude.toString())">
            </data-display-tile>
            <data-display-tile title="End" :value="optional(stats.start_latitude) + ', ' + optional(stats.start_longitude.toString())">
            </data-display-tile>
        </div>
        <div v-if="stats.is_temperature_data_available">
            <data-display-tile title="Average Temperature" :value="$converter(stats.average_temp, 'C')">
            </data-display-tile>
        </div>
        <div v-if="stats.is_cadence_data_available">
            <data-display-tile title="Average Cadence" :value="optional(stats.average_cadence.toString())">
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
            <data-display-tile title="Started At" :value="optional(stats.started_at.toString())">
            </data-display-tile>
            <data-display-tile title="Finished At" :value="optional(stats.finished_at.toString())">
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
        optional(fn) {
            try {
                return fn();
            } catch (e) {}
        },
        convertTime(time) {
            return this.runConversion(time, (t) => moment(t).format('DD/MM/YYYY HH:mm:ss'))
        },
        convertDistance(distance) {
            return this.runConversion(distance, (d) => (Math.round(((d / 1000) + Number.EPSILON) * 100) / 100) + 'km')
        },
        convertDuration(duration) {
            return this.runConversion(duration, (d) => moment.utc(d*1000).format('HH:mm:ss'))
        },
        convertElevation(elevation) {
            return this.runConversion(elevation, (e) => Math.round(e) + 'm')
        },
        convertSpeed(speed) {
            return this.runConversion(speed, (s) => Math.round((s * 3.6) * 100)/100 + 'km/h')
        },
        convertPace(pace) {
            return this.runConversion(pace, (p) => Math.round((p / 60) * 100)/100 + 'mins/km')
        },
        runConversion(value, convert) {
            return value === null ? 'N/A' : convert(value);
        }
    }
}
</script>

<style scoped>

</style>
