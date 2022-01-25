

export default {
    computed: {
        overriddenTask: {
            get() {
                return this.taskToOverrideWith ?? this.task;
            },
            set(val) {
                this.taskToOverrideWith = val;
            }
        },
        hasTask() {
            return this.overriddenTask !== null
        },
        taskStatus() {
            return this.overriddenTask?.status;
        },
        taskMessage() {
            return this.overriddenTask?.latest_message;
        },
        messages() {
            return this.overriddenTask?.messages;
        },
        isRunning() {
            return this.taskStatus === 'processing' || this.taskStatus === 'queued';
        },
        taskId() {
            return this.task?.id || null;
        }
    },
    mounted() {
        if(this.taskId !== null) {
            this.listenToTask(this.taskId);
        }
    },
    data() {
        return {
            taskToOverrideWith: null
        }
    },
    methods: {
        listenToTask(taskId) {
            window.Echo.private(`task.${taskId}`)
                .listen('.task.updated', (e) => this.overriddenTask = e.task);
        },
        stopListeningToTask(taskId) {
            Echo.leave(`orders.${taskId}`);
        }
    },
    watch: {
        taskId(newTask, oldTask) {
            if(oldTask) {
                this.stopListeningToTask(oldTask);
            }
            if(newTask) {
                this.listenToTask(newTask);
            }
        }
    }

}
