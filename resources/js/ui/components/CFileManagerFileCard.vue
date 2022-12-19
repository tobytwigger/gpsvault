<template>
    <v-card>
        <v-img
            tabindex="0"
            @click="$emit('openImage', file.image_index ?? 0)"
            @keydown.enter="$emit('openImage', file.image_index ?? 0)"
            @keydown.space="$emit('openImage', file.image_index ?? 0)"
            v-if="isImage"
            :src="route('file.preview', file.id)"
            class="white--text align-end"
            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
            height="200px"
            style="cursor: pointer;"
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

            <slot name="actions"></slot>
        </v-card-actions>
    </v-card>
</template>

<script>
import CFileFormDialog from './Activity/CFileFormDialog';
import CConfirmationDialog from './CConfirmationDialog';
export default {
    name: "CFileManagerFileCard",
    components: {CConfirmationDialog, CFileFormDialog},
    props: {
        file: {
            required: true,
            type: Object
        }
    },
    computed: {
        isImage() {
            return this.file.mimetype.startsWith('image/');
        }
    },
}
</script>

<style scoped>

</style>
