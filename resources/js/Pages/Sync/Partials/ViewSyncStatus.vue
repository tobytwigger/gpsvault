<template>
    <div>
        <ul class="bg-white rounded-lg border border-gray-200 text-gray-900 text-sm font-medium">
            <view-single-task :sync="sync" :task="task" v-for="task in taskDetails" :key="task.id"></view-single-task>
        </ul>
    </div>
</template>

<script>
import JetButton from '@/Jetstream/Button.vue'
import ViewSingleTask from './ViewSingleTask';

export default {
    name: "ViewSyncStatus",
    components: {
        ViewSingleTask,
        JetButton,
    },
    props: {
        taskDetails: {
            type: Array,
            default: () => []
        },
        sync: {
            required: true,
            type: Object
        }
    },
    computed: {
        completedTasks() {
            return this.sync.successful_tasks ?? [];
        },
        failedTasks() {
            return this.sync.failed_tasks ?? [];
        },
        taskMessages() {
            return this.sync.task_messages ?? {};
        },
        tasks() {
            return this.sync.tasks ?? [];
        },
    }
}
</script>

<style scoped>

</style>
