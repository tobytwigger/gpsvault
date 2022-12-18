import JobStatusObserver from '@tobytwigger/laravel-job-status-vue/dist/core/JobStatusObserver';
import JobStatusClient from '@tobytwigger/laravel-job-status-vue/dist/core/JobStatusClient';

/*
Must define the following data properties
- jobStatusTags
- jobStatusAlias
 */

export default {
    mounted() {
        this.setUpObserver();
    },
    destroyed() {
        this.jobStatusObserver?.cleanup();
    },
    data() {
        return {
            jobStatus: null,
            loadingStatus: false,
            initialStatusLoad: false,
            statusError: null,
            jobStatusObserver: null,
            pollInterval: 10000
        };
    },
    methods: {
        setUpObserver() {
            this.initialLoad = true;
            if (this.jobStatusObserver !== null) {
                this.jobStatusObserver.cleanup();
            }
            this.jobStatusObserver = new JobStatusObserver(this.jobStatusAlias, this.jobStatusTags);
            this.jobStatusObserver
                .poll(this.pollInterval)
                .onUpdated((jobStatus) => {
                    this.jobStatus = jobStatus;
                    this.statusError = null;
                })
                .onError((error) => (this.statusError = error.message))
                .onLoading(() => (this.loadingStatus = true))
                .onFinishedLoading(() => {
                    this.loadingStatus = false;
                    this.initialLoad = false;
                });
            this.jobStatusObserver.update();
        },
        cancel() {
            return this.signal('cancel', true);
        },
        signal(signal, cancelJob, parameters) {
            if (this.jobStatus === null) {
                return null;
            }
            return JobStatusClient.getInstance().sendSignal(this.jobStatus.id, signal, cancelJob, parameters);
        },
    },
}
