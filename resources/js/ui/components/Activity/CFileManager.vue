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
                    <c-file-manager-file-card :activity="activity" :file="file"></c-file-manager-file-card>
                </v-col>
            </v-row>
        </template>
    </v-data-iterator>
</template>

<script>
import CFileFormDialog from './CFileFormDialog';
import CFileManagerFileCard from './CFileManagerFileCard';
export default {
    name: "CFileManager",
    components: {CFileManagerFileCard, CFileFormDialog},
    props: {
        activity: {
            required: true,
            type: Object
        },
        files: {
            required: true,
            type: Array
        }
    }
}
</script>

<style scoped>

</style>
