<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Viewing {{activity.name ? activity.name : 'Unnamed Activity'}}
            </h2>
        </template>

        <confirmation-modal :show="confirmingActivityDeletion" @close="confirmingActivityDeletion = false">
            <template #title>
                Delete Activity
            </template>

            <template #content>
                Are you sure you want to delete the activity? Once the activity is deleted, all of its resources and data will be permanently deleted.

                If you haven't done so, make sure you have a backup before continuing.
            </template>

            <template #footer>
                <secondary-button @click.native="confirmingActivityDeletion = false">
                    Nevermind
                </secondary-button>

                <danger-button class="ml-2" @click.native="deleteActivity">
                    Delete Activity
                </danger-button>
            </template>
        </confirmation-modal>

        <modal :show="uploadActivityFileModal" :closeable="true" @close="uploadActivityFileModal = false">
            <form @submit.prevent="uploadFile">

                <div class="px-6 py-4">
                    <div class="text-lg">
                        Upload activity file
                    </div>

                    <div class="mt-4">
                        <p>Upload the fit/tcx/gpx recording of your ride for a deeper analysis.</p>

                        <div class="mt-4">
                            <jet-label for="file" value="Activity File" />
                            <jet-input id="file" type="file" @input="form.file = $event.target.files[0]" class="mt-1 block w-full" required />
                        </div>

                        <div class="mt-4">

                        </div>

                    </div>
                </div>

                <div class="px-12 py-4 bg-gray-100 text-right">
                    <div>
                        <jet-button class="ml-4" type="submit" :disabled="form.processing">
                            Upload
                        </jet-button>
                    </div>
                </div>
            </form>
        </modal>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="max-w-full mx-4 py-6 sm:mx-auto sm:px-6 lg:px-8">
                        <div class="text-right">
                            <a :href="route('activity.download', this.activity.id)" class="px-1">
                                <secondary-button>
                                    Download activity
                                </secondary-button>
                            </a>

                            <a class="px-1" :href="route('file.download', activity.activity_file_id)" v-if="activity.activity_file_id">
                                <secondary-button>
                                    Download activity file
                                </secondary-button>
                            </a>
                            <secondary-button class="px-1" @click.native="uploadActivityFileModal = true" :disabled="uploadActivityFileModal === true" v-else>
                                Upload activity file
                            </secondary-button>

                            <danger-button class="px-1" @click.native="confirmingActivityDeletion = true">
                                Delete activity
                            </danger-button>
                        </div>
                        <div class="sm:flex sm:space-x-4">
                            <div v-if="hasStats">
                                <data-display-tile title="Distance" :value="convertDistance(stats.distance)" :loading="!loading">
                                </data-display-tile>
                                <data-display-tile title="Total Time" :value="convertDuration(stats.duration)" :loading="!loading">
                                </data-display-tile>
                                <data-display-tile title="Elevation Gain" :value="convertElevation(stats.cumulativeElevationGain)" :loading="!loading">
                                </data-display-tile>
                                <data-display-tile title="Average Speed" :value="convertSpeed(stats.averageSpeed)" :loading="!loading">
                                </data-display-tile>
                                <data-display-tile title="Average Pace" :value="convertPace(stats.averagePace)" :loading="!loading">
                                </data-display-tile>
                            </div>
                            <div v-else>
                                <data-display-tile title="Distance" :value="convertDistance(activity.distance)" :loading="!loading">
                                </data-display-tile>
                                <data-display-tile title="Moving Time" :value="convertTime(activity.start_at)" :loading="!loading">
                                </data-display-tile>
                            </div>

                        </div>
                    </div>
                    <file-manager :activity="activity"></file-manager>

                </div>
            </div>
        </div>

    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import DataDisplayTile from './Partials/DataDisplayTile';
    import moment from 'moment';
    import JetInput from '@/Jetstream/Input.vue'
    import ConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
    import DangerButton from '@/Jetstream/DangerButton.vue'
    import SecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import Modal from '@/Jetstream/Modal.vue'
    import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import {useForm} from '@inertiajs/inertia-vue3'
    import FileManager from './Partials/FileManager';
    import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            FileManager,
            DataDisplayTile,
            AppLayout,
            JetButton,
            JetInput,
            JetLabel,
            JetValidationErrors,
            Link,
            ConfirmationModal,
            DangerButton,
            SecondaryButton,
            Modal
        },
        props: {
            activity: Object,
        },
        data() {
            return {
                stats: {},
                loading: false,
                form: useForm({
                    file: null,
                    _method: 'patch'
                }),
                confirmingActivityDeletion: false,
                uploadActivityFileModal: false
            }
        },
        methods: {
            deleteActivity() {
                this.$inertia.delete(route('activity.destroy', this.activity.id));
            },
            runConversion(value, convert) {
                return value === null ? 'N/A' : convert(value);
            },
            convertTime(time) {
                return this.runConversion(time, (time) => moment(time).format('DD/MM/YYYY HH:mm:ss'))
            },
            convertDistance(distance) {
                return this.runConversion(distance, (distance) => (Math.round(((distance / 1000) + Number.EPSILON) * 100) / 100) + 'km')
            },
            convertDuration(duration) {
                return this.runConversion(duration, (duration) => moment.utc(duration*1000).format('HH:mm:ss'))
            },
            convertElevation(elevation) {
                return this.runConversion(elevation, (elevation) => Math.round(elevation) + 'm')
            },
            convertSpeed(speed) {
                return this.runConversion(speed, (speed) => Math.round((speed * 3.6) * 100)/100 + 'km/h')
            },
            convertPace(pace) {
                return this.runConversion(pace, (pace) => Math.round((pace / 60) * 100)/100 + 'mins/km')
            },
            uploadFile() {
                this.form.post(route('activity.update', this.activity.id), {
                    onSuccess: () => {
                        this.loadStats();
                        this.form.reset();
                        this.uploadActivityFileModal = false;
                    }
                });
            },
            loadStats() {
                this.loading = true;
                axios.get('/stats/' + this.activity.id)
                    .then(response => {
                        this.stats = response.data;
                    })
                    .catch((error) => this.stats = {})
                    .then(() => this.loading = false);
            },
        },
        created() {
            this.loadStats();
        },
        computed: {
            hasStats() {
                return this.loading || Object.keys(this.stats).length > 0;
            },
            images() {
                return this.activity.files.filter(file => file.mimetype.startsWith('image'));
            }
        }
    })
</script>
