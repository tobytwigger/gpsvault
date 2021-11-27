<template>
    <li class="px-4 py-2 border-b border-gray-200 w-full rounded-t-lg">
        <div class="flex flex-row">
            <div class="row-span-2 mr-3">
                <!--Failed Icon-->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="status === 'failed'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!--Completed Icon-->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'succeeded'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!--Waiting Icon-->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'queued'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <!--Running Icon-->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-spin text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'processing'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>

                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'cancelled'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

            </div>
            <div class="flex flex-col">
                <div>{{name}}</div>
                <div>
                    <span :class="messageColour">
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
        taskDetails: {
            type: Array,
            required: false,
            default: () => []
        }
    },
    mounted() {
        window.Echo.private(`task.${this.task.id}`)
            .listen('.task.updated', (e) => this.overrideTask = e.task);
    },
    data() {
        return {
            overrideTask: null
        }
    },
    computed: {
        taskData() {
            return this.overrideTask ?? this.task;
        },
        name() {
            let details = this.taskDetails.find(t => t.id === this.taskData.task_id);
            if(details !== undefined && details.hasOwnProperty('name')) {
                return details.name;
            }
            return 'Task';
        },
        messageColour() {
            if(this.status === 'failed') {
                return 'text-red-400';
            } if(this.status === 'cancelled') {
                return 'text-orange-400';
            }
            return 'text-gray-400'
        },
        status() {
            return this.taskData.status;
        },
        message() {
            if(this.taskData.messages.length > 0) {
                return this.taskData.messages[this.taskData.messages.length - 1];
            }
            if(this.status === 'failed') {
                return 'Task failed';
            }
            if(this.status === 'failed') {
                return 'Task cancelled';
            }
            if(this.status === 'succeeded') {
                return 'Task ran successfully';
            }
            if(this.status === 'queued') {
                return 'Task in queue';
            }
            if(this.status === 'processing') {
                return 'Task running';
            }
            return '';
        }
    }
}
</script>

<style scoped>

</style>
