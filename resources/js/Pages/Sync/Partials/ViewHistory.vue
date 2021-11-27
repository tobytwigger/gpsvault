<template>
    <ul class="bg-white rounded-lg border border-gray-200 text-gray-900 text-sm font-medium">
        <li class="px-4 py-2 border-b border-gray-200 w-full rounded-t-lg" v-for="sync in syncs">
            <div class="flex flex-row" @click="$emit('view', sync)">
                <div class="row-span-2 mr-3">
    <!--                &lt;!&ndash;Failed Icon&ndash;&gt;-->
    <!--                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="status === 'failed'">-->
    <!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />-->
    <!--                </svg>-->

    <!--                &lt;!&ndash;Completed Icon&ndash;&gt;-->
    <!--                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'succeeded'">-->
    <!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />-->
    <!--                </svg>-->

    <!--                &lt;!&ndash;Waiting Icon&ndash;&gt;-->
    <!--                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'queued'">-->
    <!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />-->
    <!--                </svg>-->

    <!--                &lt;!&ndash;Running Icon&ndash;&gt;-->
    <!--                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-spin text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'processing'">-->
    <!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />-->
    <!--                </svg>-->

    <!--                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else-if="status === 'cancelled'">-->
    <!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />-->
    <!--                </svg>-->

                </div>
                <div class="flex flex-col">
                    <div>Started at {{formatDateTime(sync.started_at)}}</div>
                    <div>
                        <span>
                            {{sync.status_report}}
                        </span>
                    </div>
                </div>
            </div>


        </li>
    </ul>
</template>

<script>
import JetButton from '@/Jetstream/Button.vue'
import moment from 'moment';

export default {
    name: "ViewHistory",
    components: {
        JetButton,
    },
    props: {
        syncs: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
        }
    },
    methods: {
        formatDateTime(val) {
            return moment(val).format('DD/MM/YYYY hh:mm:ss');
        }
    },
    computed: {
    }
}
</script>

<style scoped>

</style>
