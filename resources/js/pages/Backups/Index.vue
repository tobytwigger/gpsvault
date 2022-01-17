<template>
    <c-app-wrapper :action-sidebar="true" title="Your Backups">
        <c-pagination-iterator :paginator="backups" item-key="id" :prepend="hasTask && isRunning">
            <template v-if="hasTask" #prepend>
                <v-card
                    class="mx-auto"
                    max-width="344"
                    outlined
                >
                    <v-card-title>
                        Generating backup -
                    </v-card-title>

                    <v-card-subtitle>
                        {{ taskStatus }}
                    </v-card-subtitle>

                    <v-card-text>
                        <v-row>
                            <v-col justify="center">
                                <v-list
                                    dense
                                >
                                    <v-list-item v-for="(message, index) in messages" :key="index">
                                        <v-list-item-icon>
                                            <v-icon v-if="index !== messages.length-1 && isRunning">mdi-check</v-icon>
                                            <v-progress-circular
                                                :width="2"
                                                color="red"
                                                indeterminate
                                                v-else
                                            ></v-progress-circular>
                                        </v-list-item-icon>
                                        <v-list-item-title>{{ message }}</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-card-actions>

                        <v-spacer></v-spacer>

                        <c-confirmation-dialog :loading="canceling" button-text="Cancel" title="Cancel backup?"
                                               @confirm="cancelBackup" cancel-button-text="Keep it">
                            <template v-slot:activator="{trigger,showing}">
                                <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            icon
                                            link
                                            @click="trigger"
                                            :disabled="showing"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                            <v-icon>mdi-cancel</v-icon>
                                        </v-btn>
                                    </template>
                                    Cancel
                                </v-tooltip>
                            </template>
                            Are you sure you want to cancel this backup generation?
                        </c-confirmation-dialog>

                    </v-card-actions>

                </v-card>
            </template>
            <template v-slot:default="{item}">
                <c-backup-card :backup="item"></c-backup-card>
            </template>
        </c-pagination-iterator>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <v-btn
                        :disabled="creatingBackup"
                        color="primary"
                        link
                        @click="createBackup"
                    >
                        Create a backup
                    </v-btn>
                </v-list-item>
            </v-list>
        </template>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CActivityCard from 'ui/components/Activity/CActivityCard';
import CActivityForm from 'ui/components/Activity/CActivityForm';
import CBackupCard from 'ui/components/Backup/CBackupCard';
import CPaginationIterator from 'ui/components/CPaginationIterator';
import CConfirmationDialog from 'ui/components/CConfirmationDialog';
import CBackupForm from 'ui/components/Backup/CBackupForm';
import syncs from 'ui/mixins/syncs';

export default {
    name: "Index",
    components: {
        CBackupForm,
        CConfirmationDialog, CPaginationIterator, CBackupCard, CActivityForm, CActivityCard, CAppWrapper
    },
    mixins: [syncs],
    props: {
        backups: {
            required: true,
            type: Object
        },
        task: {
            required: false,
            type: Object,
            default: null
        }
    },
    data() {
        return {
            creatingBackup: false,
            canceling: false
        }
    },
    methods: {
        createBackup() {
            this.creatingBackup = true;
            this.$inertia.post(route('backup.store'), {
                onSuccess: () => this.creatingBackup = false
            })
        },
        cancelBackup() {
            if(this.hasTask) {
                this.canceling = true;
                this.$inertia.post(route('backup.sync.cancel', this.task.sync_id), {
                    onFinish: () => this.canceling = false
                })
            }
        },

    },
    computed: {
        paginator() {
            return this.backups;
        }
    }
}
</script>

<style scoped>

</style>
