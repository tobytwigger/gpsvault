import moment from 'moment';

export default {
    computed: {
        next15Mins() {
            let now = moment();
            return now
                .add(15 - (now.minute() % 15), "minutes")
                .seconds(0)
                .format("HH:mm:ss");
        },
        nextDay() {
            return moment()
                .add(1, 'days')
                .seconds(0)
                .minutes(0)
                .hours(0)
                .format('DD/MM/YYYY');
        }
    }
}
