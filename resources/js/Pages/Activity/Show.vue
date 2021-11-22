<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Viewing {{activity.name ? activity.name : 'Unnamed Activity'}}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="max-w-full mx-4 py-6 sm:mx-auto sm:px-6 lg:px-8">
                        <div class="sm:flex sm:space-x-4">
                            <data-display-tile title="Distance" :value="distance" :loading="!hasStats">
                            </data-display-tile>
                            <data-display-tile title="Moving Time" :value="movingTime" :loading="!hasStats">
                            </data-display-tile>
                            <data-display-tile title="Total Time" :value="totalTime" :loading="!hasStats">
                            </data-display-tile>
                            <data-display-tile title="Elevation Gain" :value="elevationGain" :loading="!hasStats">
                            </data-display-tile>
                            <data-display-tile title="Average Speed" :value="averageSpeed" :loading="!hasStats">
                            </data-display-tile>
                            <data-display-tile title="Average Pace" :value="averagePage" :loading="!hasStats">
                            </data-display-tile>
                        </div>
                    </div>
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

    export default defineComponent({
        components: {
            DataDisplayTile,
            AppLayout
        },
        props: {
            activity: Object,
        },
        data() {
            return {
                stats: {}
            }
        },
        methods: {
        },
        created() {
            axios.get('/stats/' + this.activity.id)
                .then(response => {
                    this.stats = response.data;
                });
        },
        computed: {
            hasStats() {
                Object.keys(this.stats).length > 0;
            },
            distance() {
                let distance = this.stats.distance ?? null;
                if(distance !== null) {
                    distance = (Math.round(((distance / 1000) + Number.EPSILON) * 100) / 100) + 'km';
                }
                return distance;
            },
            movingTime() {
                return 'N/A';
            },
            totalTime() {
                let duration = this.stats.duration ?? null;
                if(duration !== null) {
                    duration = moment.utc(duration*1000).format('HH:mm:ss')
                }
                return duration;
            },
            elevationGain() {
                let gain = this.stats.cumulativeElevationGain ?? null;
                if(gain !== null) {
                    gain = Math.round(gain) + 'm';
                }
                return gain;
            },
            averageSpeed() {
                let speed = this.stats.averageSpeed ?? null;
                if(speed !== null) {
                    speed = Math.round((speed * 3.6) * 100)/100 + 'km/h';
                }
                return speed;
            },
            averagePage() {
                let pace = this.stats.averagePace ?? null;
                if(pace !== null) {
                    pace = Math.round((pace / 60) * 100)/100 + 'mins/km';
                }
                return pace;
            }
        }
    })
</script>
