<template>
    <file-pond
        name="file"
        id="file"
        :required="required"
        type="file"
        ref="pond"
        class-name="my-pond"
        label-idle="Drop files here..."
        :allow-multiple="multiple"
        :accepted-file-types="accept"
        v-bind:files="files"
        @init="handleFilePondInit"
    />
</template>

<script>
// Import Vue FilePond
import vueFilePond, { setOptions } from "vue-filepond";

// Import FilePond styles
import "filepond/dist/filepond.min.css";

// Import FilePond plugins
// Please note that you need to install these plugins separately

// Import image preview plugin styles
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";

// Create component
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview
);

export default {
    name: "CFileInput",
    components: {
        FilePond
    },
    props: {
        id: {
            required: true,
            type: String
        },
        name: {
            required: true,
            type: String
        },
        required: {
            required: false,
            type: Boolean,
            default: false
        },
        multiple: {
            required: false,
            type: Boolean,
            default: false
        },
        accept: {
            required: false,
            type: String,
            default: 'image/*, video/*'
        },
        value: {
            required: false,
            default: null
        },
        hint: {
            required: false,
            type: String
        },
        errorMessages: {
            required: false,
            type: Array
        }
    },
    data() {
        return {
            files: []
        }
    },
    watch: {
        files: {
            deep: true,
            handler: function(files) {
                let processedFiles = files.filter(i => i.serverId !== null);
                if(processedFiles.length === 0) {
                    this.$emit('input', this.multiple ? [] : null)
                } else {
                    let serverIds = processedFiles.map(i => i.serverId);
                    this.$emit('input', this.multiple ? serverIds : serverIds[0]);
                }
            }
        }
    },
    methods: {
        handleFilePondInit: function () {
            setOptions({
                server: {
                    url: route('filepond.chunk'),
                    process: '/process',
                    revert: '/process',
                    patch: "?patch=",
                    headers: {
                        'X-CSRF-TOKEN': this.$page.props.csrf,
                        'X-REQUESTED-WITH': 'XMLHttpRequest'
                    }
                },
                chunkUploads: true
            })
            const filepondsArray = document.getElementsByClassName(
                "filepond--root"
            );

            filepondsArray.forEach((filepond) => {
                filepond.addEventListener('FilePond:processfile', e => {
                    console.log(e.detail.file);
                    this.files.push(e.detail.file);
                })

                filepond.addEventListener('FilePond:removefile', e => {
                    this.files = this.files.filter(f => f.id !== e.detail.file.id);
                })

            });
        }
    }
}
</script>

<style scoped>

</style>
