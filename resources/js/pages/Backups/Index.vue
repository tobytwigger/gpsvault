<template>
    <c-app-wrapper title="Your Backups" :menu-items="menuItems">
<!--        <c-job-status job="create-full-backup" :tags="{user_id: $page.props.user.id}" :poll-interval="2000" :onCompleted="$inertia.reload()">-->
<!--            <template v-slot:incomplete>-->
<!--                <v-progress-circular-->
<!--                    indeterminate-->
<!--                    color="primary"-->
<!--                ></v-progress-circular>-->
<!--            </template>-->
<!--        </c-job-status>-->

        <c-iterator :infinite-scroll="true" :paginator="backups" item-key="id" :prepend="isGeneratingBackup">
            <template v-slot:prepend>
<!--                <c-loading-backup-card-->
<!--                    :job-status="jobStatus"-->
<!--                    v-if="isGeneratingBackup"-->
<!--                    @cancel="cancel">-->
<!--                </c-loading-backup-card>-->
            </template>
            <template v-slot:default="{item}">
                <c-backup-card :backup="item"></c-backup-card>
            </template>
        </c-iterator>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CActivityCard from 'ui/components/Activity/CActivityCard';
import CActivityForm from 'ui/components/Activity/CActivityForm';
import CBackupCard from 'ui/components/Backup/CBackupCard';
import CPaginationIterator from 'ui/reusables/table/CPaginationIterator';
import CConfirmationDialog from 'ui/components/CConfirmationDialog';
import CBackupForm from 'ui/components/Backup/CBackupForm';
import CJobStatus from '../../ui/components/CJobStatus';
import usesJobStatus from '../../ui/mixins/usesJobStatus';
import CLoadingBackupCard from '../../ui/components/Backup/CLoadingBackupCard';
import CIterator from '../../ui/reusables/table/CIterator';

export default {
    name: "Index",
    mixins: [usesJobStatus],
    components: {
        CIterator,
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
                    // this.$inertia.reload();
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
        isGeneratingBackup() {
            return this.jobStatus !== null
                && this.jobStatus.status !== 'succeeded';
        },
        menuItems() {
            return [
                {
                    title: 'Create Backup',
                    disabled: this.isGeneratingBackup,
                    icon: 'mdi-autorenew',
                    action: () => {
                        this.createBackup();
                    }
                }
            ]
        }
    }
}
</script>

<style scoped>

</style>
