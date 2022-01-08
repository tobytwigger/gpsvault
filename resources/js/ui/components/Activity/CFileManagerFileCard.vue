<template>
    <v-card>
        <v-img
            v-if="isImage"
            :src="file.preview_url"
            class="white--text align-end"
            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
            height="200px"
        >
            <v-card-title v-text="file.title"></v-card-title>
            <v-card-subtitle v-text="file.caption" v-if="file.caption"></v-card-subtitle>
        </v-img>

        <v-card-text v-else>
            <v-card-title v-text="file.title"></v-card-title>
            <v-card-subtitle v-text="file.caption" v-if="file.caption"></v-card-subtitle>
        </v-card-text>

        <v-card-actions>
            <v-btn icon :href="route('file.download', file.id)">
                <v-icon>mdi-download</v-icon>
            </v-btn>

            <c-file-form-dialog :activity="activity" :old-file="file" text="Update information about the file" title="Update file">
                <template v-slot:activator="{trigger,showing}">
                    <v-btn
                        icon
                        @click.stop="trigger"
                        :disabled="showing"
                    >
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                </template>
            </c-file-form-dialog>

            <c-confirmation-dialog button-text="Delete" :loading="deleting" :title="deleteModalTitle" @confirm="deleteFile">
                <template v-slot:activator="{trigger,showing}">
                    <v-btn icon @click.stop="trigger" :loading="deleting" :disabled="showing">
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </template>
                Are you sure you want to delete this file? Once it is deleted you won't be able to download it, so make sure you have a backup.
            </c-confirmation-dialog>
        </v-card-actions>
    </v-card>
    <!--                    'path', 'filename', 'size', 'title', 'caption', 'mimetype', 'disk', 'extension', 'disk', 'user_id', 'type', 'hash'-->
</template>

<script>
import CFileFormDialog from './CFileFormDialog';
import CConfirmationDialog from '../CConfirmationDialog';
export default {
    name: "CFileManagerFileCard",
    components: {CConfirmationDialog, CFileFormDialog},
    props: {
        activity: {
            required: true,
            type: Object
        },
        file: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            deleting: false
        }
    },
    computed: {
        isImage() {
            return this.file.mimetype.startsWith('image/');
        },
        deleteModalTitle() {
            return 'Delete file' + (this.file.title ? ' ' + this.file.title : '') + '?';
        }
    },
    methods: {
        deleteFile() {
            this.deleting = true;
            this.$inertia.delete(route('activity.file.destroy', [this.activity.id, this.file.id]), {
                onFinish: () => this.deleting = false
            });
        }
    }
}
</script>

<style scoped>

</style>
