import units from './units';

export default {
    mixins: [units],
    computed: {
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
        positionSummary() {
            return this.summary(['distance', 'startLatitude', 'startLongitude', 'endLatitude', 'endLongitude']);
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
                    disabled: true,
                    data: [
                        {value: this.distance, label: 'total'},
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
                }
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
