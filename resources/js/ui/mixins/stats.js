import units from './units';

// Must define an `allStats` property on your Vue component

export default {
    mixins: [units],
    data() {
        return {
            dataSource: null
        }
    },
    computed: {
        hasStats() {
            return this.stats !== null;
        },
        dataSources() {
            let sortingArr = this.$setting.stats_order_preference;
            return this.allStats.map(s => s.integration).sort((a, b) => sortingArr.indexOf(a) - sortingArr.indexOf(b));
        },
        activeDataSource: {
            get() {
                if (this.allStats.length === 0) {
                    return null;
                }
                if (this.dataSource && this.dataSources.indexOf(this.dataSource) > -1) {
                    return this.dataSource;
                }
                if (this.dataSources.length > 0) {
                    return this.dataSources[0];
                }
                return null;
            },
            set(val) {
                this.dataSource = val;
            }
        },
        stats() {
            if (this.activeDataSource !== null) {
                return this.allStats.find(s => s.integration === this.activeDataSource) || null
            }
            return null;
        },
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
            return {value: this.stats?.start_point?.coordinates[0].toString() || null, unit: 'lat'};
        },
        startLongitude() {
            return {value: this.stats?.start_point?.coordinates[1].toString() || null, unit: 'long'};
        },
        endLatitude() {
            return {value: this.stats?.end_point?.coordinates[0].toString() || null, unit: 'lat'};
        },
        endLongitude() {
            return {value: this.stats?.end_point?.coordinates[1].toString() || null, unit: 'long'};
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
        },
        statSchema() {
            let schema = [
                {
                    icon: 'mdi-ruler',
                    title: 'Distance',
                    label: 'distance',
                    disabled: false,
                    pointLabel: 'cumulative_distance',
                    data: [
                        {value: this.distance, label: 'total'},
                    ]
                },
                {
                    icon: 'mdi-clock',
                    title: 'Time',
                    label: 'time',
                    pointLabel: 'time',
                    disabled: false,
                    data: [
                        {value: this.movingTime, label: 'moving'},
                        {value: this.duration, label: 'total'},
                    ]
                },
                {
                    icon: 'mdi-gauge',
                    title: 'Speed',
                    label: 'speed',
                    pointLabel: 'speed',
                    disabled: false,
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
                    pointLabel: 'elevation',
                    disabled: false,
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
                    lineColour: '#9e1a1a',
                    pointLabel: 'heart_rate',
                    disabled: false,
                    data: [
                        {value: this.maxHeartrate, label: 'max'},
                        {value: this.avgHeartrate, label: 'avg'},
                    ]
                },
                {
                    icon: 'mdi-lightning-bolt',
                    title: 'Power',
                    label: 'calories',
                    pointLabel: 'calories',
                    disabled: false,
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
                    pointLabel: 'cadence',
                    disabled: false,
                    data: [
                        {value: this.avgCadence, label: 'avg'},
                    ]
                },
                {
                    icon: 'mdi-thermometer',
                    title: 'Temperature',
                    label: 'temperature',
                    pointLabel: 'temperature',
                    disabled: false,
                    data: [
                        {value: this.averageTemperature, label: 'avg'},
                    ]
                },
            ].map(stat => {
                stat.data = stat.data.filter(d => d.value !== null);
                return stat;
            }).filter(stat => stat.data.length > 0);
            if(this.limit) {
                schema.slice(0, this.limit);
            }
            return schema;
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
