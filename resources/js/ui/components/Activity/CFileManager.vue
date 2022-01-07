<template>
    <v-data-iterator
        :items="files"
        item-key="id"
        :items-per-page="8"
        hide-default-footer
    >
        <template v-slot:default="{ items, isExpanded, expand }">
            <v-row>
                <v-col
                    v-for="file in items"
                    :key="file.id"
                    cols="12"
                    sm="6"
                    md="4"
                    lg="3"
                >
                    <v-card>
                        <v-img
                            v-if="isImage(file)"
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

                            <v-btn icon @click.prevent="deleteFile(file.id)">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
<!--                    'path', 'filename', 'size', 'title', 'caption', 'mimetype', 'disk', 'extension', 'disk', 'user_id', 'type', 'hash'-->
                </v-col>
            </v-row>
        </template>
    </v-data-iterator>
</template>

<script>
import CFileFormDialog from './CFileFormDialog';
export default {
    name: "CFileManager",
    components: {CFileFormDialog},
    props: {
        activity: {
            required: true,
            type: Object
        },
        files: {
            required: true,
            type: Array
        }
    },
    methods: {
        isImage(file) {
            return file.mimetype.startsWith('image/');
        },
        deleteFile(id) {
            this.$inertia.delete(route('activity.file.destroy', [this.activity.id, id]));
        }
    }
}
</script>

<style scoped>

</style>
