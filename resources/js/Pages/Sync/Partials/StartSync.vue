<template>
    <div>
        <ul class="bg-white rounded-lg border border-gray-200 text-gray-900 text-sm font-medium">
            <task
                v-for="(task, index) in filteredTaskDetails"
                :value="task.form"
                :index="index"
                @input="updateTask(task.id, $event)"
                :task="task"
                :key="task.id"
            ></task>
        </ul>
    </div>
</template>

<script>
import Task from './Task';
import {cloneDeep} from 'lodash';

export default {
    name: "StartSync",
    components: {
        Task,
    },
    data() {
        return {
            tasks: {}
        }
    },
    watch: {
        formData: {
            handler: function() {
                this.$emit('input', {tasks: this.formData});
            },
            deep: true
        }
    },
    props: {
        taskDetails: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        updateTask(taskId, form) {
            this.tasks[taskId] = form;
        }
    },
    computed: {
        filteredTaskDetails() {
            let tasks = cloneDeep(this.taskDetails.map(task => {
                if(!this.tasks[task.id]) {
                    this.tasks[task.id] = {
                        id: task.id, enabled: true, config: {}, valid: true
                    }
                }
                task.form = this.tasks[task.id];
                return task;
            }));
            tasks.sort((a, b) => {
                if(a.disabled === b.disabled) {
                    return 0;
                }
                if(a.disabled === true && b.disabled === false) {
                    return 1;
                }
                if(a.disabled === false && b.disabled === true) {
                    return -1;
                }
            });
            return tasks;
        },
        formData() {
            return Object.keys(this.tasks)
                .filter(key => this.tasks[key].enabled)
                .map(key => this.tasks[key]);
        }
    }
}
</script>

<style scoped>

</style>
