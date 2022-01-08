import * as convertUnit from 'convert-units';
import moment from 'moment';

export default {
    data() {
        return {
            unitCasts: {
                heartrate: (val) => {
                    return {value: val, unit: 'bpm'}
                },
                calories: (val) => {
                    return {value: val, unit: 'kcal'}
                },
                cadence: (val) => {
                    return {value: val, unit: 'rpm'}
                },
                duration: (val) => {
                    return {value: moment.utc(val * 1000).format('HH:mm:ss'), unit: 'h'}
                }
            }
        }
    },
    computed: {
        units() {
            return {
                system: {
                    speed: 'm/s',
                    temperature: 'C',
                    energy: 'kJ',
                    power: 'W',
                    distance: 'm',
                    elevation: 'm',
                    page: 's/m'
                },
                user: {
                    speed: 'km/h',
                    temperature: 'C',
                    energy: 'kJ',
                    power: 'W',
                    distance: 'km',
                    elevation: 'm',
                    pace: 'min/km'
                }
            }
        },
    },
    methods: {
        convert(value, unit) {
            if(value === null) {
                return null;
            }
            if(this.unitCasts.hasOwnProperty(unit)) {
                return this.unitCasts[unit](value);
            }
            if(!this.hasSystemUnit(unit) || !this.hasUserUnit(unit)) {
                throw new Error('Unit ' + unit + ' is not defined');
            }
            if(this.getSystemUnit(unit) === this.getUserUnit(unit)) {
                return {value: value, unit: this.getSystemUnit(unit)};
            }
            return {value: this.round(
                convertUnit(value).from(this.getSystemUnit(unit)).to(this.getUserUnit(unit))
            ), unit: this.getUserUnit(unit)}
        },
        round(value) {
            return Math.round((value + Number.EPSILON) * 100) / 100
        },
        hasSystemUnit(unit) {
            return this.units.system.hasOwnProperty(unit);
        },
        hasUserUnit(unit) {
            return this.units.user.hasOwnProperty(unit);
        },
        getSystemUnit(unit) {
            return this.units.system[unit];
        },
        getUserUnit(unit) {
            return this.units.user[unit];
        }
    }
}
