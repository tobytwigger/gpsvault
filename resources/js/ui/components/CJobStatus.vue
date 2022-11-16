<template>
    <job-status :job-alias="job" :tags="tags" :poll-interval="60000" ref="job">
        <template v-slot:default="params">
            <div v-if="params === null || params.complete === true">
                <slot></slot>
            </div>
            <div v-else>
                <slot name="incomplete"></slot>
            </div>
        </template>
    </job-status>
</template>

<script>
import JobStatusObserver from '@tobytwigger/laravel-job-status-vue/dist/core/JobStatusObserver';

export default {
    name: "CJobStatus",
    props: {
        job: {
            type: String,
            required: true,
        },
        tags: {
            type: Object,
            required: false,
            default: () => {
                return {}
            }
        }
    },
    data() {
        return {
            jobStatusObserver: null
        }
    },
    mounted() {
        if (this.jobStatusObserver !== null) {
            this.jobStatusObserver.cleanup();
        }
        this.jobStatusObserver = new JobStatusObserver(this.job, this.tags);
        this.jobStatusObserver
            .poll(this.pollInterval)
            .onUpdated((jobStatus) => {
                if(this.status === 'completed') {
                    this.$emit('onCompleted');
                }
            })
        this.jobStatusObserver.update();
    },
    destroyed() {
        this.jobStatusObserver?.cleanup();
    }

}
</script>

<style scoped>

</style>
