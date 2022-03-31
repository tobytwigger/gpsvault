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
                },
                datetime: (val) => {
                    return {value: moment(val).format('DD/MM/YYYY HH:mm:ss'), unit: ''}
                }
            }
        }
    },
    computed: {
        units() {
            let units = {
                system: {
                    speed: 'm/s',
                    temperature: 'C',
                    energy: 'kJ',
                    power: 'W',
                    distance: 'm',
                    elevation: 'm',
                    pace: 's/m'
                }
            };
            if(this.unitSystem === 'imperial') {
                units.user = {
                    speed: 'm/h',
                    temperature: 'C',
                    energy: 'kJ',
                    power: 'W',
                    distance: 'mi',
                    elevation: 'ft',
                    pace: 'min/mi'
                }
            } else if(this.unitSystem === 'metric') {
                units.user = {
                    speed: 'km/h',
                    temperature: 'C',
                    energy: 'kJ',
                    power: 'W',
                    distance: 'km',
                    elevation: 'm',
                    pace: 'min/km'
                };
            } else {
                units.user = units.system;
            }
            return units;
        },
        unitSystem() {
            return this.$setting.unit_system || 'metric';
        }
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
            return this.getSystemUnit(unit) !== null;
        },
        hasUserUnit(unit) {
            return this.getUserUnit(unit) !== null;
        },
        getSystemUnit(unit) {
            return this.units.system.hasOwnProperty(unit) ? this.units.system[unit] : null;
        },
        getUserUnit(unit) {
            return this.units.user[unit];
        }
    }
}
