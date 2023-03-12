<template>
    <div>
        <v-row
            align="center"
            class="fill-height"
            justify="center">
            <v-col
                cols="12"
                md="4"
                sm="8">
                <v-card>
                    <v-card-title class="justify-center">
                        <span class="primary--text">{{title}}</span>
                    </v-card-title>

                    <v-card-subtitle v-if="jobStatus.messages.length > 0">
                        {{ jobStatus.messages[0].message }}
                    </v-card-subtitle>

                    <v-card-text>
                        <v-progress-linear
                            :color="progressBarColour"
                            buffer-value="0"
                            :indeterminate="jobStatus.status === 'queued'"
                            :value="jobStatus.percentage"
                            stream
                        ></v-progress-linear>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: "CLoadingFromJobStatus",
    props: {
        jobStatus: {
            required: true,
            type: Object
        },
        title: {
            required: true,
            type: String
        }
    },
    computed: {
        progressBarColour() {
            if(this.jobStatus.status === 'failed') {
                return 'red';
            }
            if(this.jobStatus.status === 'cancelled') {
                return 'orange';
            }
            return 'green';
        }
    }
}
</script>

<style scoped>

</style>
