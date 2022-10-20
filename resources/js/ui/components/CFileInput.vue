<template>
    <file-pond
        :name="'file-input-' + id"
        :id="'file-input-' + id"
        :required="required"
        type="file"
        ref="pond"
        :server="serverOptions"
        class-name="my-pond"
        label-idle="Drop files here..."
        allow-multiple="true"
        :accepted-file-types="accept"
        v-bind:files="value"
        @init="handleFilePondInit"
    />
</template>

<script>
// Import Vue FilePond
import vueFilePond from "vue-filepond";

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
            type: Array,
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
            serverOptions: {
                url: route('filepond.chunk'),
                process: '/process',
                revert: '/process',
                patch: "?patch=",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        }
    },
    methods: {
        handleFilePondInit: function () {
            const filepondsArray = document.getElementsByClassName(
                "filepond--root"
            );

            filepondsArray.forEach((filepond) => {
                filepond.addEventListener(
                    "FilePond:updatefiles",
                    (e) => {
                        let files = e.detail.items.map(i => {
                            console.log(i);
                            return i.serverId
                        });
                        console.log(files);
                        this.$emit('input', files);
                    }
                );
            });
        }
    }
}
</script>

<style scoped>

</style>
