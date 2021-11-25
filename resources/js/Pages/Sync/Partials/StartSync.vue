<template>
    <div>
        <ul class="list-disc">
            <li v-for="task in taskDetails" :key="task.id">
                {{task.name}} - {{ task.description }}.
            </li>
        </ul>
    </div>
</template>

<script>
import JetButton from '@/Jetstream/Button.vue'

export default {
    name: "StartSync",
    components: {
        JetButton,
    },
    props: {
        taskDetails: {
            type: Array,
            default: () => []
        }
    },
}
</script>

<style scoped>

</style>
