<template>
    <div>
        <c-file-manager :files="routeModel.files">
            <template v-slot:actions="{file}">
                <c-route-file-form-dialog :route-model="routeModel" :old-file="file" text="Update information about the file" title="Update file">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn
                            icon
                            @click.stop="trigger"
                            :disabled="showing"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </template>
                </c-route-file-form-dialog>

                <c-confirmation-dialog button-text="Delete" :loading="deleting[file.id]" :title="deleteModalTitle(file)" @confirm="deleteFile(file)">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn icon @click.stop="trigger" :loading="deleting[file.id]" :disabled="showing">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                    Are you sure you want to delete this file? Once it is deleted you won't be able to download it, so make sure you have a backup.
                </c-confirmation-dialog>
            </template>
        </c-file-manager>
    </div>
</template>

<script>
import CFileManager from '../CFileManager';
import CRouteFileFormDialog from './CRouteFileFormDialog';
import CConfirmationDialog from '../CConfirmationDialog';
export default {
    name: "CManageRouteMedia",
    components: {CConfirmationDialog, CRouteFileFormDialog, CFileManager},
    props: {
        routeModel: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            deleting: {}
        }
    },
    methods: {
        deleteFile(file) {
            this.deleting[file.id] = true;
            this.$inertia.delete(route('route.file.destroy', [this.routeModel.id, file.id]), {
                onFinish: () => this.deleting[file.id] = false
            });
        },
        deleteModalTitle(file) {
            return 'Delete file' + (file.title ? ' ' + file.title : '') + '?';
        }
    }
}
</script>

<style scoped>

</style>
