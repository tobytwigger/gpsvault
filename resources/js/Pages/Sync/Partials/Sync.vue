<template>
    <div>
        <div class="px-6 py-4">
            <div class="mt-4">
                <view-sync-status v-if="viewingSync" :sync="viewingSync" :task-details="taskDetails"></view-sync-status>
                <start-sync v-else :task-details="taskDetails"></start-sync>
            </div>
        </div>

        <div class="px-12 py-4 text-right">
            <div>
                <jet-button class="ml-4 bg-red-600" @click="cancelSync" v-if="isSyncing">
                    <div class="flex justify-center">
                        <span class="ml-1">Cancel</span>
                    </div>
                </jet-button>

                <jet-button class="ml-4" @click="startSync" v-if="viewPreviousSync === null">
                    <div class="flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" v-if="isSyncing" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span v-if="isSyncing" class="ml-1">Syncing</span>
                        <span class="ml-1" v-else-if="viewPreviousSync === null">Start Sync</span>
                        <span class="ml-1" v-else>Prepare Sync</span>
                    </div>
                </jet-button>

                <jet-button class="ml-4" @click="viewPreviousSync = null" v-else>
                    <div class="flex justify-center">
                        <span class="ml-1">Prepare Sync</span>
                    </div>
                </jet-button>
            </div>
        </div>

        <h2>Previous Syncs</h2>
        <ul class="list-disc">
            <li v-if="isSyncing && !isViewingCurrentSync">
                <a href="#" @click.prevent="viewPreviousSync = null">Current sync</a>
            </li>
            <li v-for="previousSync in previous">
                <a href="#" @click.prevent="viewPreviousSync = previousSync">Sync started {{ getAsDate(previousSync.created_at) }}</a>
            </li>
        </ul>
    </div>
</template>

<script>
import JetButton from '@/Jetstream/Button.vue'
import Modal from '@/Jetstream/Modal';
import ViewSyncStatus from './ViewSyncStatus';
import StartSync from './StartSync';
import moment from 'moment';

export default {
    name: "Sync",
    components: {
        JetButton,
        Modal,
        ViewSyncStatus,
        StartSync
    },
    props: {
        taskDetails: {
            required: false,
            type: Array,
            default: () => []
        },
        current: {
            required: false,
            type: Object,
            default: null
        },
        previous: {
            required: false,
            type: Array,
            default: () => []
        },
        userId: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            viewPreviousSync: null,
            listening: []
        }
    },
    mounted() {
        // window.setInterval(() => {
        //     if(this.isSyncing) {
        //         this.refreshSyncData();
        //     }
        // }, 1000)
        window.Echo.private(`user.${this.userId}.sync`)
            .listen('sync.updated', (e) => {
                console.log('updated')
            })
            .listen('sync.finished', (e) => {
                console.log(e);
                // this.refreshSyncData(e.sync.id);
            });
    },
    methods: {
        refreshSyncData(viewSyncId) {
            this.$inertia.reload({only: ['current', 'previous', 'integrations']})
        },
        startSync() {
            if(!this.isSyncing) {
                this.$inertia.post(route('sync.start'));
            }
        },
        cancelSync() {
            if(this.isSyncing) {
                this.$inertia.post(route('sync.cancel'));
            }
        },
        getAsDate(date) {
            return moment.duration(moment().diff(date)).humanize() + ' ago';
        },
    },
    computed: {
        isSyncing() {
            return this.current !== null;
        },
        isViewingCurrentSync() {
            return this.viewPreviousSync === null;
        },
        viewingSync() {
            return this.viewPreviousSync ?? this.current;
        }
    }
}
</script>

<style scoped>

</style>
