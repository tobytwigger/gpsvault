<template>
    <sync-layout :title="currentPageTitle" :data="currentPageTitleData">
        <template #titleAction>
            <div v-if="pageType === 'sync'">
                <jet-button class="ml-4 bg-red-600" @click="cancelSync">
                    <div class="flex justify-center">
                        <span class="ml-1">Cancel</span>
                    </div>
                </jet-button>
            </div>
            <div v-else-if="pageType === 'new'">
                <jet-button class="ml-4" @click="startSync" :disabled="!newSyncIsReady">
                    <div class="flex justify-center">
                        <span class="ml-1">Start Sync</span>
                    </div>
                </jet-button>
            </div>
        </template>

        <template #belowTitle>
            <div v-if="pageType === 'sync'">
                <sync-progress :tasks="currentOverriddenTasks"></sync-progress>
            </div>
            <div v-else-if="pageType === 'history'">
                <sync-progress :tasks="viewPreviousSync.tasks"></sync-progress>
            </div>
        </template>

        <template #menu>
            <li class="hover:bg-gray-50 cursor-pointer p-1" :class="{'bg-gray-100 shadow-sm': viewPreviousSync === null}" @click.prevent="viewPreviousSync = null">
                <span v-if="isSyncing" class="flex flex-row content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 animate-spin text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Current Sync
                </span>
                <span v-else class="flex flex-row content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Sync
                </span>
            </li>
            <li class="hover:bg-gray-50 cursor-pointer p-1" :class="{'bg-gray-100 shadow-sm': viewPreviousSync && viewPreviousSync.id === previousSync.id}" v-for="previousSync in previous" @click.prevent="viewPreviousSync = previousSync">
                <span class="flex flex-row content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Sync started {{ getAsDate(previousSync.started_at) }}
                </span>
            </li>
        </template>

        <div v-if="viewingSync">
            <ul class="bg-white rounded-lg border border-gray-200 text-gray-900 text-sm font-medium w-full">
                <view-single-task :task-details="taskDetails" :task="checkForOverriddenTask(task)" v-for="task in viewingSync.tasks" :key="task.id" @taskUpdated="overrideTask"></view-single-task>
            </ul>
        </div>
        <start-sync v-else :task-details="taskDetails" @input="syncForm = $event"></start-sync>

    </sync-layout>
</template>

<script>
import ViewSingleTask from './ViewSingleTask';

import JetButton from '@/Jetstream/Button.vue'
import Modal from '@/Jetstream/Modal';
import StartSync from './StartSync';
import moment from 'moment';
import SyncLayout from './SyncLayout';
import SyncProgress from './SyncProgress';
import {cloneDeep} from 'lodash';

export default {
    name: "Sync",
    components: {
        SyncProgress,
        SyncLayout,
        JetButton,
        Modal,
        StartSync,
        ViewSingleTask
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
    created() {
        this.now = moment().toISOString();
        window.setInterval(() => this.now = moment().toISOString(), 1000);
    },
    watch: {
        current() {
            if(this.current !== null) {
                window.Echo.private(`sync.${this.current.id}`)
                    .listen('.sync.finished', (e) => {
                        this.$inertia.remember(e.sync.id, 'viewing-sync');
                        this.$inertia.reload({
                            onSuccess: (page) => {
                                let viewingId = this.$inertia.restore('viewing-sync');
                                this.viewPreviousSync = this.previous.find(p => p.id === viewingId) ?? null;

                            }
                        })
                    });
            }
        }
    },
    data() {
        return {
            viewPreviousSync: null,
            listening: [],
            syncForm: {},
            now: null,
            overriddenTasks: [],
        }
    },
    methods: {
        startSync() {
            if(!this.isSyncing) {
                this.$inertia.post(route('sync.start'), this.syncForm, {errorBag: 'startSync'});
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
        overrideTask(task) {
            let currentTask = this.overriddenTasks.find(t => t.id === task.id);
            if(currentTask) {
                this.overriddenTasks.splice(this.overriddenTasks.indexOf(currentTask), 1, task);
            } else {
                this.overriddenTasks.push(task);
            }
        },
        checkForOverriddenTask(task) {
            return this.overriddenTasks.find(t => t.id === task.id) ?? task;
        }
    },
    computed: {
        currentOverriddenTasks() {
            let current = cloneDeep(this.current);
            let tasks = [];
            if(this.isSyncing) {
                for(let i=0;i<current.tasks.length;i++) {
                    tasks.push(this.checkForOverriddenTask(current.tasks[i]));
                }
                return tasks;
            }
            return [];
        },
        currentWithOverriddenTasks() {
            let current = cloneDeep(this.current);
            if(current) {
                current.tasks = this.currentOverriddenTasks;
            }
            return current;
        },
        isSyncing() {
            return this.current !== null;
        },
        viewingSync() {
            return this.viewPreviousSync ?? this.currentWithOverriddenTasks;
        },
        newSyncIsReady() {
            return (this.syncForm.tasks ?? []).filter(task => task.valid === false).length === 0 && (this.syncForm.tasks ?? []).length > 0;
        },
        pageType() {
            if(this.viewPreviousSync !== null) {
                return 'history';
            }
            if(this.current !== null) {
                return 'sync';
            }
            return 'new';
        },
        currentPageTitle() {
            if(this.pageType === 'history') {
                return 'Sync #' + this.viewingSync.id;
            }
            if(this.pageType === 'sync') {
                return 'Syncronising';
            }
            return 'New Sync';
        },
        currentPageTitleData() {
            if(this.pageType === 'history') {
                return [
                    {label: 'Started At', value: moment(this.viewingSync.started_at).format('DD/MM/YY HH:mm:ss')},
                    {
                        label: 'Runtime',
                        value: this.viewingSync.runtime + 's'
                    }
                ];
            }
            if(this.pageType === 'sync') {
                return [
                    {label: 'Started At', value: moment(this.current.started_at).format('DD/MM/YY HH:mm:ss')},
                    {label: 'Runtime', value: moment.duration(moment(this.now).diff(this.current.started_at, 'seconds')) + 's'}
                ];
            }
            return [
                {label: 'Selected', value: (this.syncForm.tasks ?? []).length + '/' + this.taskDetails.length},
            ];
        }
    }
}
</script>

<style scoped>

</style>
