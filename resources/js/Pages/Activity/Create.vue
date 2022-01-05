<template>
    <v-app-layout title="New Activity">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Upload</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <upload-file @submitted="uploadFile"></upload-file>
                    </div>
                </div>
            </div>
        </div>
    </v-app-layout>
</template>

<script>
    import { defineComponent } from 'vue'

    import UploadFile from './Partials/UploadFile';

    export default defineComponent({
        components: {
            UploadFile,
        },
        methods: {
            uploadFile(form) {
                form.post(route('activity.store'));
            }
        }
    })
</script>
