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

            <slot name="actions"></slot>
        </v-card-actions>
    </v-card>
    <!--                    'path', 'filename', 'size', 'title', 'caption', 'mimetype', 'disk', 'extension', 'disk', 'user_id', 'type', 'hash'-->
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
    methods: {
    }
}
</script>

<style scoped>

</style>
