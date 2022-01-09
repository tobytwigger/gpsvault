import units from './units';

export default {
    mixins: [units],
    computed: {
        maxSpeed() {
            return this.convert(this.stats?.max_speed?.toString() || null, 'speed');
        },
        avgSpeed() {
            return this.convert(this.stats?.average_speed?.toString() || null, 'speed');
        },
        calories() {
            return this.convert(this.stats?.calories?.toString() || null, 'calories');
        },
        kilojoules() {
            return this.convert(this.stats?.kilojoules?.toString() || null, 'energy');
        },
        avgWatts() {
            return this.convert(this.stats?.average_watts?.toString() || null, 'power');
        },
        maxHeartrate() {
            return this.convert(this.stats?.max_heartrate?.toString() || null, 'heartrate');
        },
        avgHeartrate() {
            return this.convert(this.stats?.average_heartrate?.toString() || null, 'heartrate');
        },
        distance() {
            return this.convert(this.stats?.distance?.toString() || null, 'distance');
        },
        startLatitude() {
            return {value: this.stats?.start_latitude.toString() || null, unit: 'lat'};
        },
        startLongitude() {
            return {value: this.stats?.start_longitude.toString() || null, unit: 'long'};
        },
        endLatitude() {
            return {value: this.stats?.end_latitude.toString() || null, unit: 'lat'};
        },
        endLongitude() {
            return {value: this.stats?.end_longitude.toString() || null, unit: 'long'};
        },
        averageTemperature() {
            return this.convert(this.stats?.average_temp?.toString() || null, 'temperature');
        },
        avgCadence() {
            return this.convert(this.stats?.average_cadence?.toString() || null, 'cadence');
        },
        avgPace() {
            return this.convert(this.stats?.average_pace?.toString() || null, 'pace');
        },
        movingTime() {
            return this.convert(this.stats?.moving_time?.toString() || null, 'duration');
        },
        duration() {
            return this.convert(this.stats?.duration?.toString() || null, 'duration');
        },
        startedAt() {
            return this.convert(this.stats?.started_at?.toString() || null, 'datetime');
        },
        finishedAt() {
            return this.convert(this.stats?.finished_at?.toString() || null, 'datetime');
        },
        elevationLoss() {
            return this.convert(this.stats?.elevation_loss?.toString() || null, 'elevation');
        },
        elevationGain() {
            return this.convert(this.stats?.elevation_gain?.toString() || null, 'elevation');
        },
        minAltitude() {
            return this.convert(this.stats?.min_altitude?.toString() || null, 'elevation');
        },
        maxAltitude() {
            return this.convert(this.stats?.max_altitude?.toString() || null, 'elevation');
        },
        powerSummary() {
            return this.summary(['calories', 'kilojoules', 'avgWatts']);
        },
        heartrateSummary() {
            return this.summary(['maxHeartrate', 'avgHeartrate']);
        },
        positionSummary() {
            return this.summary(['distance', 'startLatitude', 'startLongitude', 'endLatitude', 'endLongitude']);
        },
        temperatureSummary() {
            return this.summary(['averageTemperature']);
        },
        cadenceSummary() {
            return this.summary(['avgCadence']);
        },
        speedSummary() {
            return this.summary(['maxSpeed', 'avgSpeed', 'avgPace']);
        },
        timeSummary() {
            return this.summary(['movingTime', 'duration', 'startedAt', 'finishedAt']);
        },
        elevationSummary() {
            return this.summary(['elevationLoss', 'elevationGain', 'minAltitude', 'maxAltitude']);
        },
        humanStartedAt() {
            return this.stats?.human_started_at || null;
        },
        humanEndedAt() {
            return this.stats?.human_ended_at || null;
        }
    },
    methods: {
        summary(properties) {
            let summary = {};
            properties.forEach(p => summary[p] = this[p]);
            return summary;
        },
    }
}
