<template>
    <c-app-wrapper :action-sidebar="true" title="Your Backups">
<!--        <c-job-status job="create-full-backup" :tags="{user_id: $page.props.user.id}" :poll-interval="2000" :onCompleted="$inertia.reload()">-->
<!--            <template v-slot:incomplete>-->
<!--                <v-progress-circular-->
<!--                    indeterminate-->
<!--                    color="primary"-->
<!--                ></v-progress-circular>-->
<!--            </template>-->
<!--        </c-job-status>-->

        <c-pagination-iterator :paginator="backups" item-key="id" :prepend="showLoadingBackup">
            <template v-slot:prepend>
                <c-loading-backup-card
                    :job-status="jobStatus"
                    v-if="showLoadingBackup"
                    @cancel="cancel">
                </c-loading-backup-card>
            </template>
            <template v-slot:default="{item}">
                <c-backup-card :backup="item"></c-backup-card>
            </template>
        </c-pagination-iterator>

        {{jobStatus}}

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <v-btn
                        data-hint="You can add a new backup by clicking here"
                        color="primary"
                        link
                        @click="createBackup"
                    >
                        Create a backup
                    </v-btn>
<!--                    <c-job-status job="create-full-backup" :tags="{user_id: $page.props.user.id}" :poll-interval="2000">-->
<!--                        <template v-slot:incomplete>-->
<!--                            <v-btn-->
<!--                                data-hint="You can add a new backup by clicking here"-->
<!--                                :disabled="true"-->
<!--                                color="primary"-->
<!--                                :loading="true"-->
<!--                            >-->
<!--                                Create a backup-->
<!--                            </v-btn>-->

<!--                        </template>-->
<!--                        <v-btn-->
<!--                            data-hint="You can add a new backup by clicking here"-->
<!--                            color="primary"-->
<!--                            link-->
<!--                            @click="createBackup"-->
<!--                        >-->
<!--                            Create a backup-->
<!--                        </v-btn>-->
<!--                    </c-job-status>-->


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
import usesJobStatus from '../../ui/mixins/usesJobStatus';
import CLoadingBackupCard from '../../ui/components/Backup/CLoadingBackupCard';

export default {
    name: "Index",
    mixins: [usesJobStatus],
    components: {
        CLoadingBackupCard,
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
    watch: {
        jobStatus: {
            deep: true,
            handler: function() {
                if(this.jobStatus.status === 'succeeded') {
                    this.$inertia.reload();
                }
            }
        }
    },
    data() {
        return {
            jobStatusAlias: 'create-full-backup',
            jobStatusTags: {user_id: this.$page.props.user.id}
        }
    },
    methods: {
        createBackup() {
            this.$inertia.post(route('backup.store'), {})
        },
        test() {
            console.log('HI');
        }
    },
    computed: {
        paginator() {
            return this.backups;
        },
        showLoadingBackup() {
            return this.jobStatus !== null
                && this.jobStatus.status !== 'succeeded';
        }
    }
}
</script>

<style scoped>

</style>
