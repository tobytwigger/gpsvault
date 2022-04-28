<template>
    <c-app-wrapper :action-sidebar="true" title="Your Backups">
        <c-pagination-iterator :paginator="backups" item-key="id">
            <template v-slot:default="{item}">
                <c-backup-card :backup="item"></c-backup-card>
            </template>
        </c-pagination-iterator>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <v-btn
                        data-intro="You can add a new backup by clicking here"
                        data-hint="This is your hint?"
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

export default {
    name: "Index",
    components: {
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
            creatingBackup: false,
        }
    },
    methods: {
        createBackup() {
            this.creatingBackup = true;
            this.$inertia.post(route('backup.store'), {}, {
                onFinish: () => this.creatingBackup = false
            })
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
