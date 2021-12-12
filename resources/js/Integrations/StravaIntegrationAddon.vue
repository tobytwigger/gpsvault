<template>
    <modal :closeable="true" :show="viewStravaSyncStatus" @close="viewStravaSyncStatus = false">

            <div class="px-6 py-4">
                <div class="text-lg">
                    Strava Sync Status
                </div>

                <div class="mt-4">
                    <p>
                        We are currently refreshing the following data in the background.

                        You can see if an activity is being loaded by viewing it.
                    </p>
                    <ul class="list-disc">
                        <li>Kudos: {{activitiesLoadingKudos}}</li>
                        <li>Comments: {{activitiesLoadingComments}}</li>
                        <li>Stats: {{activitiesLoadingStats}}</li>
                        <li>Photos: {{activitiesLoadingPhotos}}</li>
                        <li>Basic Data: {{activitiesLoadingBasicData}}</li>
                    </ul>

                    <p>Photos and the raw activity files must be imported manually with the full export.</p>

                    <p>There are {{activitiesWithoutFiles}} activities that are linked to Strava but are missing the raw recording</p>

                    <p>There are {{activitiesWithoutPhotos}} activities that should have photos attached</p>

                    <p v-if="timeUntilReady === null">
                        Strava is ready
                    </p>
                    <p v-else>
                        Strava will be available in {{formattedTimeUntilReady}}
                    </p>
                </div>
            </div>

            <div class="px-12 py-4 bg-gray-100 text-right">
                <div>
                    <button class="ml-4" type="button" @click="viewStravaSyncStatus = false">
                        OK
                    </button>
                </div>
            </div>
    </modal>

    <a href="#" @click.prevent="viewStravaSyncStatus = true" class="text-sm text-gray-400">View sync status</a>
</template>

<script>
import Modal from '@/Jetstream/Modal.vue'
import moment from 'moment';

export default {
    name: "StravaIntegrationAddon",
    components: {
        Modal
    },
    props: {
        activitiesLoadingKudos: Number,
        activitiesLoadingComments: Number,
        activitiesLoadingStats: Number,
        activitiesLoadingPhotos: Number,
        activitiesLoadingBasicData: Number,
        activitiesWithoutFiles: Number,
        activitiesWithoutPhotos: Number,
        timeUntilReady: {
            required: false,
            type: Number,
            default: null
        }
    },
    data() {
        return {
            viewStravaSyncStatus: false
        }
    },
    computed: {
        formattedTimeUntilReady() {
            if(this.timeUntilReady === null) {
                return null;
            }
            return moment.duration(moment().add(this.timeUntilReady, 'seconds').diff(moment())).humanize();
        }
    }
}
</script>

<style scoped>

</style>
