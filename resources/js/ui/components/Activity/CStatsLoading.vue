<template>
<!--
- If we have no running jobs, show 'no stats'.
- If we have strava OR analysis job running, show 'analysing stats in the background'
-->

    <div>
        <span v-if="gpsVaultJobStatus === null && stravaJobStatus === null">
            No ride analysis available
        </span>
        <span v-else>
            Analysing stats in the background
        </span>
    </div>

</template>

<script>
import JobStatusObserver from '@tobytwigger/laravel-job-status-vue/dist/core/JobStatusObserver';

export default {
    name: "CStatsLoading",
    mounted() {
        this.setUpObservers();
    },
    destroyed() {
        this.jobStatusObserver?.cleanup();
    },
    data() {
        return {
            gpsVaultJobStatus: null,
            stravaJobStatus: null,

            gpsVaultJobStatusObserver: null,
            stravaJobStatusObserver: null,

            pollInterval: 1000,
        }
    },
    methods: {
        setUpObservers() {
            if (this.gpsVaultJobStatusObserver !== null) {
                this.gpsVaultJobStatusObserver.cleanup();
            }
            if (this.stravaJobStatusObserver !== null) {
                this.stravaJobStatusObserver.cleanup();
            }
            this.gpsVaultJobStatusObserver = new JobStatusObserver('', {});
            this.stravaJobStatusObserver = new JobStatusObserver('', {});
            this.gpsVaultJobStatusObserver.poll(this.pollInterval)
                .onUpdated((jobStatus) => this.gpsVaultJobStatus = jobStatus)
                .onError((error) => (this.statusError = error.message));
            this.stravaJobStatusObserver.poll(this.pollInterval)
                .onUpdated((jobStatus) => this.stravaJobStatus = jobStatus)
                .onError((error) => (this.statusError = error.message));
            this.jobStatusObserver.update();
        }
    }
}
</script>

<style scoped>

</style>
