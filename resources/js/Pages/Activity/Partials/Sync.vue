<template>
    <div class="text-right">
        <jet-button class="ml-4" @click="showSync = true">
            <div class="flex justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" :class="{'animate-spin': isSyncing}" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span v-if="isSyncing" class="ml-1">Syncing</span><span class="ml-1" v-else>Sync</span>
            </div>
        </jet-button>

        <modal :show="showSync" :closeable="true" @close="showSync = false">
            <div class="px-6 py-4">
                <div class="text-lg">
                    Sync
                </div>

                <div class="mt-4">
                    <p>Syncing xyz.</p>

                    <p class="text-sm mt-4">Select & sync.</p>

                    <ul class="list-disc">
                        <li v-for="task in sync.tasks" :key="task.id">
                            {{task.name}} - {{ task.description }}.
                            <span v-if="failedTasks.indexOf(task.id) > -1">Failed</span>
                            <span v-else-if="completedTasks.indexOf(task.id) > -1">Completed</span>
                            <span v-else-if="tasks.indexOf(task.id) > -1">Running</span>
                        </li>
                    </ul>

                </div>
            </div>

            <div class="px-12 py-4 bg-gray-100 text-right">
                <div>
                    <jet-button @click="startSync" v-if="!isSyncing">
                        Start Sync
                    </jet-button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
import JetButton from '@/Jetstream/Button.vue'
import Modal from '@/Jetstream/Modal';

export default {
    name: "Sync",
    components: {
        JetButton,
        Modal
    },
    props: {
        sync: {
            required: true,
            type: Object
        }
    },
    created() {
        if(this.sync.openSync) {
            this.showSync = true;
        }
    },
    methods: {
        startSync() {
            this.$inertia.post(route('sync'));
        }
    },
    data() {
        return {
            showSync: false
        }
    },
    computed: {
        isSyncing() {
            return this.sync.sync !== null;
        },
        completedTasks() {
            return this.sync.sync.hasOwnProperty('successful_tasks') ? this.sync.sync.successful_tasks : [];
        },
        failedTasks() {
            return this.sync.sync.hasOwnProperty('failed_tasks') ? Object.keys(this.sync.sync.failed_tasks) : [];
        },
        tasks() {
            return this.sync.sync.hasOwnProperty('tasks') ? this.sync.sync.tasks : [];
        }
    }
}
</script>

<style scoped>

</style>
