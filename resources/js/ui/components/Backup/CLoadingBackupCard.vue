<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >
        <v-card-title>
            <span v-if="jobStatus.isFinished === false">Backing up...</span>
            <span v-else-if="jobStatus.status === 'failed'">Backup failed</span>
            <span v-if="jobStatus.status === 'cancelled'">Backup cancelled</span>
        </v-card-title>

        <v-card-subtitle v-if="jobStatus.lastMessage">
            {{ jobStatus.lastMessage }}
        </v-card-subtitle>

        <v-card-text>
            <v-progress-linear
                :color="progressBarColour"
                buffer-value="0"
                :value="jobStatus.percentage"
                stream
            ></v-progress-linear>
        </v-card-text>

        <!--            <v-list-item-avatar-->
        <!--                tile-->
        <!--                size="80"-->
        <!--                color="grey"-->
        <!--            ></v-list-item-avatar>-->
        <!--        </v-list-item>-->

        <v-card-actions>

            <v-spacer></v-spacer>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            icon
                            @click="$emit('cancel')"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                    </template>
                    Cancel
                </v-tooltip>

            </v-card-actions>
        </v-card-actions>

    </v-card>
</template>

<script>
import moment from 'moment';
import * as convertUnit from 'convert-units';
import CConfirmationDialog from '../CConfirmationDialog';
import CBackupForm from './CBackupForm';


export default {
    name: "CLoadingBackupCard",
    components: {CBackupForm, CConfirmationDialog},
    props: {
        jobStatus: {
            required: true,
            type: Object
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
