<template>
    <div class="relative pt-1">
        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-400">
            <div
                :style="{width: queued + '%'}"
                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gray-400 transition duration-500 ease-in-out transition-width"
            >Queued</div>
            <div
                :style="{width: processing + '%'}"
                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-400"
            >Running</div>
            <div
                :style="{width: succeeded + '%'}"
                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-400"
            >Succeeded</div>
            <div
                :style="{width: failed + '%'}"
                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-400"
            >Failed</div>
            <div
                :style="{width: cancelled + '%'}"
                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-orange-400"
            >Cancelled</div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SyncProgress",
    props: {
        tasks: {
            required: true,
            type: Array
        }
    },
    methods: {
        getTaskPercentWithStatus(status) {
            return (this.tasks.filter(task => task.status === status).length / this.tasks.length) * 100;
        }
    },
    computed: {
        succeeded() {
            return this.getTaskPercentWithStatus('succeeded');
        },
        failed() {
            return this.getTaskPercentWithStatus('failed');
        },
        cancelled() {
            return this.getTaskPercentWithStatus('cancelled');
        },
        queued() {
            return this.getTaskPercentWithStatus('queued');
        },
        processing() {
            return this.getTaskPercentWithStatus('processing');
        }
    }
}
</script>

<style scoped>

</style>
