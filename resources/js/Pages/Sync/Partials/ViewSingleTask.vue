<template>
    <li class="px-4 py-2 border-b border-gray-200 w-full rounded-t-lg">
        <div class="flex flex-row">
            <div class="row-span-2 mr-3">
                <!--Failed Icon-->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="status === 'failed'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!--Completed Icon-->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'succeeded'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!--Waiting Icon-->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="inQueue">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!--Running Icon-->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-spin text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'running'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>

            </div>
            <div class="flex flex-col">
                <div>{{task.name}}</div>
                <div>
                    <span :class="{'text-red-400': status === 'failed', 'text-gray-400': status === 'running' || status === 'succeeded'}">
                        {{message}}
                    </span>
                </div>
            </div>
        </div>


    </li>
</template>

<script>
import JetButton from '@/Jetstream/Button.vue'

export default {
    name: "ViewSingleTask",
    components: {
        JetButton,
    },
    props: {
        task: {
            type: Object,
            required: true
        },
        sync: {
            required: true,
            type: Object
        }
    },
    computed: {
        status() {
            if((this.sync.failed_tasks ?? []).indexOf(this.task.id) > -1) {
                return 'failed';
            }
            if((this.sync.successful_tasks ?? []).indexOf(this.task.id) > -1) {
                return 'succeeded';
            }
            if((this.sync.tasks ?? []).indexOf(this.task.id) > -1) {
                return 'running';
            }
            return 'error';
        },
        inQueue() {
            return this.status === 'running' && !(this.sync.task_messages ?? {}).hasOwnProperty(this.task.id)
        },
        message() {
            if((this.sync.task_messages ?? {}).hasOwnProperty(this.task.id)) {
                return this.sync.task_messages[this.task.id];
            }
            return 'In queue';
        }
    }
}
</script>

<style scoped>

</style>
