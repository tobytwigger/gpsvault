<template>
    <div>
        <form @submit.prevent="uploadFiles" v-if="uploadFile">

            Upload files or media associated with the activity

            <div class="mt-4">
                <jet-label for="files" value="Files"/>
                <jet-input id="files" type="file" ref="filesUpload" multiple @input="form.files = $event.target.files"
                           class="mt-1 block w-full" required/>
            </div>

            <div class="mt-4">
                <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Store Files
                </jet-button>
                <button type="reset" @click.prevent="resetForm" class="ml-4 inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    Cancel
                </button>
            </div>
        </form>

        <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm" v-if="!uploadFile">
                <a href="#" @click.prevent="uploadFile = true" class="w-0 flex-1 flex items-center">
                    <!-- Heroicon name: solid/paper-clip -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="ml-2 flex-1 w-0 truncate">Upload a File</span>
                </a>
            </li>


            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm" v-for="file in files">
                <div class="w-0 flex flex-1 items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-2">
                        <a :href="route('file.download', file.id)">
                            {{ file.title ? file.title : file.filename}}
                        </a>
                        <span class="mt-1 text-sm text-gray-500" v-if="file.caption">
                             - {{file.caption}}
                        </span>
                    </div>

                </div>
                <div class="ml-4 flex-shrink-0">
                    <a :href="route('file.download', file.id)" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Download
                    </a>
                    <span class="text-indigo-600">|</span>
                    <a href="#" @click.prevent="fileToEdit = file" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Edit
                    </a>
                    <span class="text-indigo-600">|</span>
                    <a href="#" @click.prevent="deleteFile(file.id)" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Delete
                    </a>
                </div>
            </li>
        </ul>

        <edit-file :file="fileToEdit" :activity-id="activity.id" @close="fileToEdit = null">

        </edit-file>
    </div>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3';
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetButton from '@/Jetstream/Button.vue'
import EditFile from './EditFile';

export default {
    name: "FileManager",
    components: {
        EditFile,
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors
    },
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            form: useForm({
                files: null,
                _method: 'patch'
            }),
            uploadFile: false,
            fileToEdit: null
        }
    },
    computed: {
        files() {
            return this.activity.files
        }
    },
    methods: {
        resetForm() {
            this.uploadFile = false;
            this.form.reset();
            this.$refs.filesUpload.value = null;
        },
        uploadFiles() {
            this.form.post(route('activity.update', this.activity.id), {
                onSuccess: () => this.resetForm()
            })
        },
        deleteFile(id) {
            this.$inertia.delete(route('activity.file.destroy', [this.activity.id, id]));
        }
    }
}
</script>

<style scoped>

</style>
