<template>
    <c-app-wrapper :action-sidebar="true" title="Your Backups">
        <c-job-status job="create-full-backup" :tags="{user_id: $page.props.user.id}" :poll-interval="2000" :onCompleted="$inertia.reload()">
            <template v-slot:incomplete>
                <v-progress-circular
                    indeterminate
                    color="primary"
                ></v-progress-circular>
            </template>
        </c-job-status>

        <c-pagination-iterator :paginator="backups" item-key="id">
            <template v-slot:default="{item}">
                <c-backup-card :backup="item"></c-backup-card>
            </template>
        </c-pagination-iterator>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <c-job-status job="create-full-backup" :tags="{user_id: $page.props.user.id}" :poll-interval="2000">
                        <template v-slot:incomplete>
                            <v-btn
                                data-hint="You can add a new backup by clicking here"
                                :disabled="true"
                                color="primary"
                                :loading="true"
                            >
                                Create a backup
                            </v-btn>

                        </template>
                        <v-btn
                            data-hint="You can add a new backup by clicking here"
                            color="primary"
                            link
                            @click="createBackup"
                        >
                            Create a backup
                        </v-btn>
                    </c-job-status>


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
import CJobStatus from '../../ui/components/CJobStatus';

export default {
    name: "Index",
    components: {
        CJobStatus,
        CBackupForm,
        CConfirmationDialog, CPaginationIterator, CBackupCard, CActivityForm, CActivityCard, CAppWrapper
    },
    props: {
        backups: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
        }
    },
    methods: {
        createBackup() {
            this.$inertia.post(route('backup.store'), {})
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
