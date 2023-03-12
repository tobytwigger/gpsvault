<template>
    <c-app-wrapper title="Your Backups" :menu-items="menuItems">
        <c-iterator :infinite-scroll="true" :paginator="backups" item-key="id" :prepend="showBackupPrepend">
            <template v-slot:prepend>
                <c-loading-backup-card
                    :job-status="jobStatus"
                    v-if="showBackupPrepend"
                    @cancel="cancel">
                </c-loading-backup-card>
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
import CLoadingBackupCard from '../../ui/components/Backup/CLoadingBackupCard';
import CIterator from '../../ui/reusables/table/CIterator';
import {client} from '@tobytwigger/laravel-job-status-js';

export default {
    name: "Index",
    components: {
        CIterator,
        CLoadingBackupCard,
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
            handler: function(newVal, oldVal) {
                if(newVal !== null && oldVal !== null && newVal?.status !== oldVal?.status) {
                    this.$inertia.reload();
                }
            }
        }
    },
    data() {
        return {
            jobStatusTags: {user_id: this.$page.props.user.id},
            jobStatus: null,
            listener: null,
            isLoading: false,
            isUpdating: false
        }
    },
    mounted() {
        this.setupListener();
    },
    beforeDestroy() {
        this.removeListener();
    },
    methods: {
        cancel() {
            if(this.generatingArchive) {
                client.runs.signal(this.jobStatus.id, 'cancel')
                    .shouldCancelJob()
                    .send();
            }
        },
        createBackup() {
            this.$inertia.post(route('backup.store'), {})
        },
        setupListener() {
            this.removeListener();
            this.listener = client.runs.search()
                .whereAlias('create-full-backup')
                .whereTag('user_id', this.$page.props.user.id)
                .listen()
                .onStartingInitialLoad(() => this.loading = true)
                .onStartingUpdate(() => this.isUpdating = true)
                .onUpdated((runs) => {
                    this.jobStatus = runs.total > 0 ? runs.data[0] : null;
                })
                .onFinishingUpdate(() => this.isUpdating = false)
                .onFinishingInitialLoad(() => this.loading = false)
                .start()
        },
        removeListener() {
            if(this.listener !== null) {
                this.listener.stop();
                this.listener = null;
            }
        }
    },
    computed: {
        generatingArchive() {
            return this.jobStatus !== null && ['queued', 'started'].includes(this.jobStatus.status);
        },
        showBackupPrepend() {
            return this.generatingArchive || (this.jobStatus !== null && ['cancelled', 'failed'].includes(this.jobStatus.status));
        },
        menuItems() {
            return [
                {
                    title: 'Create Backup',
                    disabled: this.generatingArchive,
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
