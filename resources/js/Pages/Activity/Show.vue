<template>
<span
    v-if="isLoadingPhotos"
    class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">Loading photos</span>
<span
    v-if="isLoadingDetails"
    class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">Loading details</span>

<span
    v-if="isLoadingComments"
    class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">Loading comments</span>

<span
    v-if="isLoadingKudos"
    class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">Loading kudos</span>

<span
    v-if="isLoadingAnalysis"
    class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">Loading analysis</span>

<a :href="'https://www.strava.com/activities/' + stravaId"
    v-if="activity.linked_to.indexOf('strava') !== -1"
    class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">View on strava</a>


<select-data-source v-model="selectedDataSource" :data-sources="dataSources">

</select-data-source>

                    <page-tabs :menu-items="menuItems">
                        <template #summary>
                            This is a test
                        </template>

                        <template #analysis>
                            <stats :stats="stats"></stats>
                            <vue-map :stats="stats"></vue-map>
                            <generic-chart :stats="stats"></generic-chart>
                        </template>

                        <template #social>
                            <div>
                                <div v-if="activity.strava_comments.length > 0">
                                    <ul>
                                        <li v-for="comment in activity.strava_comments">
                                            {{ comment.name }}: {{ comment.text }} (posted
                                            {{ formatDateTime(comment.posted_at) }})
                                        </li>
                                    </ul>
                                </div>
                                <span v-if="activity.strava_kudos.length === 1">
                                    Kudos from {{ activity.strava_kudos[0].name }}.
                                </span>
                                <span v-if="activity.strava_kudos.length === 2">
                                    Kudos from {{ activity.strava_kudos[0].name }} and {{ activity.strava_kudos[1].name }}.
                                </span>
                                <span v-if="activity.strava_kudos.length === 3">
                                    Kudos from {{ activity.strava_kudos[0].name }}, {{ activity.strava_kudos[1].name }} and {{ activity.strava_kudos[2].name }}.
                                </span>
                                <span v-if="activity.strava_kudos.length > 3">
                                    Kudos from {{ activity.strava_kudos[0].name }}, {{ activity.strava_kudos[1].name }} and {{ activity.strava_kudos.length - 2 }} more.
                                </span>
                            </div>
                        </template>

                        <template #files>
                            <file-manager :activity="activity"></file-manager>
                        </template>
                    </page-tabs>
</template>

<script>
import moment from 'moment';
import JetInput from '@/Jetstream/Input.vue'
import ConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import DangerButton from '@/Jetstream/DangerButton.vue'
import SecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetLabel from '@/Jetstream/Label.vue'
import Modal from '@/Jetstream/Modal.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetButton from '@/Jetstream/Button.vue'
import {Link} from '@inertiajs/inertia-vue'
import FileManager from './Partials/FileManager';
import SelectDataSource from './Partials/SelectDataSource';
import Map from './Partials/Charts/Map';
import GenericChart from './Partials/GenericChart';
import Stats from './Partials/Stats';
import PageTabs from '../../Jetstream/PageTabs';
import VAppLayout from '../../ui/layouts/VAppLayout';

export default {
    components: {
        VAppLayout,
        PageTabs,
        SelectDataSource,
        FileManager,
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        Stats,
        Modal,
        GenericChart,
        'vue-map': Map
    },
    props: {
        activity: Object,
    },
    data() {
        return {
            menuItems: [
                {title: 'Summary', id: 'summary'},
                {title: 'Analysis', id: 'analysis'},
                {title: 'Social', id: 'social'},
                {title: 'Files', id: 'files'}
            ],
            selectedDataSource: 'php'
        }
    },
    mounted() {
        if (this.activity.stats && Object.keys(this.activity.stats).length > 0) {
            if (!this.activity.stats.hasOwnProperty('php')) {
                this.selectedDataSource = Object.keys(this.activity.stats)[0];
            }
        }
    },
    methods: {
        formatDateTime(datetime) {
            return moment(datetime).format('DD/MM/YYYY HH:mm:ss');
        },
    },
    computed: {
        activityName() {
            return this.activity.name ?? 'Unnamed Activity';
        },
        images() {
            return this.activity.files.filter(file => file.mimetype.startsWith('image'));
        },
        isLoadingPhotos() {
            return this.activity.additional_data.strava_is_loading_photos ?? false;
        },
        isLoadingDetails() {
            return this.activity.additional_data.strava_is_loading_details ?? false;
        },
        isLoadingComments() {
            return this.activity.additional_data.strava_is_loading_comments ?? false;
        },
        isLoadingKudos() {
            return this.activity.additional_data.strava_is_loading_kudos ?? false;
        },
        isLoadingAnalysis() {
            return this.activity.additional_data.strava_is_loading_stats ?? false;
        },
        stravaId() {
            return this.activity.additional_data.strava_id ?? null;
        },
        hasStats() {
            return this.stats !== null;
        },
        dataSources() {
            return Object.keys(this.activity.stats);
        },
        activeDataSource() {
            if (this.activity.stats.length === 0) {
                return null;
            }
            if (this.selectedDataSource) {
                return this.selectedDataSource;
            }
            if (Object.keys(this.activity.stats).length > 0) {
                return Object.keys(this.activity.stats)[0];
            }
            return null;
        },
        stats() {
            if (this.activeDataSource !== null && this.activity.stats.hasOwnProperty(this.activeDataSource)) {
                return this.activity.stats[this.activeDataSource];
            }
            return null;
        }
    }
}
</script>
