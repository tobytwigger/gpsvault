<template>
    <job-status job-alias="process-podcast" :poll-interval="250">
        <template v-slot:default="params">
            <div v-if="params.status === 'queued'">
                <cycle-store-demo-card title="Processing queued" subtitle="Your podcast will be processed shortly.">
                    <template v-slot:actions>
                        <v-btn @click="params.cancel">Cancel</v-btn>
                    </template>
                </cycle-store-demo-card>
            </div>
            <div v-else-if="params.status === 'started'">
                <cycle-store-demo-card title="Processing queued" :subtitle="(params.lastMessage ?? 'Your podcast will be processed shortly.')">
                    <v-progress-circular
                        :rotate="-90"
                        :size="100"
                        :width="15"
                        :value="params.percentage"
                        color="primary"
                    >
                        {{ params.percentage }}%
                    </v-progress-circular>

                    <template v-slot:actions>
                        <v-btn @click="params.cancel">Cancel</v-btn>
                    </template>
                </cycle-store-demo-card>
            </div>
            <div v-else-if="params.status === 'failed'">
                <cycle-store-demo-card title="Podcast failed." :subtitle="params.lastMessage">
                    <template v-slot:actions>
                        <v-btn @click="retry">Retry</v-btn>
                    </template>
                </cycle-store-demo-card>
            </div>
            <div v-else-if="params.status === 'cancelled'">
                <cycle-store-demo-card title="Cancelled" :subtitle="params.lastMessage">
                    <template v-slot:actions>
                        <v-btn @click="retry">Retry</v-btn>
                    </template>
                </cycle-store-demo-card>
            </div>
            <div v-else-if="params.status === 'succeeded'">
                <cycle-store-demo-card title="Podcast uploaded" :subtitle="params.lastMessage">
                    <template v-slot:actions>
                        <v-btn @click="reset">Reset</v-btn>
                    </template>
                </cycle-store-demo-card>
            </div>
        </template>

        <template v-slot:empty="{loading, initialLoad}">
            <cycle-store-demo-card title="Upload a podcast" subtitle="Upload your podcast below to get started. Need to sort initial loading stuff.">
                <v-file-input
                    label="Podcast"
                    hint="Upload an mp4 file"
                    :disabled="initialLoad"
                ></v-file-input>
                <template v-slot:actions>
                    <v-btn @click="retry">Upload</v-btn>
                </template>
            </cycle-store-demo-card>
        </template>

    </job-status>
</template>

<script>
import CycleStoreDemoCard from './CycleStoreDemoCard';
export default {
    name: "CycleStoreDemo",
    components: {CycleStoreDemoCard},
    methods: {
        upload() {
            axios.post('/podcast/fake').then(response => console.log(response));
        },
        reset() {
            axios.post('/podcast/reset').then(response => console.log(response));
        },
        retry() {
            axios.post('/podcast').then(response => console.log(response));
        }
    }
}
</script>

<style scoped>

</style>
