<template>
    <div>
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
                        <c-file-manager-file-card :file="file" @openImage="showImg">
                            <template #actions>
                                <slot name="actions" v-bind:file="file">

                                </slot>
                            </template>
                        </c-file-manager-file-card>
                    </v-col>
                </v-row>
            </template>
        </v-data-iterator>
        <vue-easy-lightbox
            :visible="showLightbox"
            :imgs="imageSrcs"
            :index="0"
            @hide="showLightbox = false"
        ></vue-easy-lightbox>
    </div>
</template>

<script>
import CFileFormDialog from './Activity/CFileFormDialog';
import CFileManagerFileCard from './CFileManagerFileCard';
export default {
    name: "CFileManager",
    components: {CFileManagerFileCard, CFileFormDialog},
    props: {
        files: {
            required: true,
            type: Array
        }
    },
    data() {
        return {
            showLightbox: false,
            imageIndex: null
        }
    },
    computed: {
        filesWithImageIndex() {
            let imageIndex = 0;
            return this.files.map(f => {
                if(f.mimetype.includes('image')) {
                    f.image_index = imageIndex;
                    imageIndex++;
                }
                return f;
            })
        },
        imageSrcs() {
            return this.files.filter(f => f.mimetype.includes('image')).map(i => route('file.preview', {file: i.id, highResolution: true}));
        }
    },
    methods: {
        showImg(index) {
            this.imageIndex = index;
            this.showLightbox = true;
        }
    }
}
</script>

<style scoped>

</style>
