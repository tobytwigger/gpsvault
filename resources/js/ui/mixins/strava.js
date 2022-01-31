import moment from 'moment';

export default {
    computed: {
        next15Mins() {
            let now = moment();
            return now
                .add(15 - (now.minute() % 15), "minutes")
                .seconds(0)
                .format("HH:mm");
        },
        nextDay() {
            return moment()
                .add(1, 'days')
                .seconds(0)
                .minutes(0)
                .hours(0)
                .format('DD/MM/YYYY');
        },
        nextDayIn() {
            return moment.duration(
                moment()
                    .add(1, 'days')
                    .seconds(0)
                    .minutes(0)
                    .hours(0)
                    .diff(moment())
            ).humanize();
        },
        isLoadingPhotos() {
            if(this.activity) {
                return this.activity.additional_data?.strava_is_loading_photos ?? false;
            }
            return null
        },
        isLoadingDetails() {
            if(this.activity) {
                return this.activity.additional_data?.strava_is_loading_details ?? false;
            }
            return null
        },
        isLoadingComments() {
            if(this.activity) {
                return this.activity.additional_data?.strava_is_loading_comments ?? false;
            }
            return null
        },
        isLoadingKudos() {
            if(this.activity) {
                return this.activity.additional_data?.strava_is_loading_kudos ?? false;
            }
            return null
        },
        isLoadingAnalysis() {
            if(this.activity) {
                return this.activity.additional_data?.strava_is_loading_stats ?? false;
            }
            return null
        },
        stravaId() {
            if(this.activity) {
                return this.activity.additional_data?.strava_id ?? null;
            }
            return null
        },
        hasComments() {
            if(this.activity) {
                return this.activity.strava_comments && this.activity.strava_comments.length > 0;
            }
            return null;
        },
        comments() {
            if(this.activity) {
                return this.activity.strava_comments || [];
            }
            return null;
        },
        commentsCount() {
            if(this.activity) {
                return this.activity.strava_comments?.length ?? 0;
            }
            return null;
        },
        hasKudos() {
            if(this.activity) {
                return this.activity.strava_kudos?.length > 0 ?? false
            }
            return null;
        },
        kudosCount() {
            if(this.activity) {
                return this.activity.strava_kudos?.length ?? 0;
            }
            return null;
        },
        kudos() {
            if(this.activity) {
                return this.activity.strava_kudos || [];
            }
            return null;
        }
    }
}
