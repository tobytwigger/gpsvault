<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Viewing {{activity.name ? activity.name : 'Unnamed Activity'}}
            </h2>
        </template>

        <a :href="route('activity.track.download', activity.id)" v-if="activity.activity_file_id">
            Download track file
        </a>

        <a :href="route('activity.download', activity.id)">
            Download activity
        </a>

        <div class="py-12" v-if="hasStats">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="max-w-full mx-4 py-6 sm:mx-auto sm:px-6 lg:px-8">
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

                                <form @submit.prevent="uploadFile">

                                    <div class="mt-4">
                                        <jet-label for="file" value="Activity File" />
                                        <jet-input id="file" type="file" @input="form.file = $event.target.files[0]" class="mt-1 block w-full" required />
                                    </div>

                                    <div class="mt-4">
                                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Upload
                                        </jet-button>
                                    </div>
                                </form>
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
    import JetLabel from '@/Jetstream/Label.vue'
    import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import {useForm} from '@inertiajs/inertia-vue3'
    import FileManager from './Partials/FileManager';

    export default defineComponent({
        components: {
            FileManager,
            DataDisplayTile,
            AppLayout,
            JetButton,
            JetInput,
            JetLabel,
            JetValidationErrors
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
                })
            }
        },
        methods: {
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
                    onSuccess: () => this.loadStats()
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
