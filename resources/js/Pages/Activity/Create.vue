<template>
    <v-app-layout title="New Activity">

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
    import VAppLayout from '../../ui/layouts/VAppLayout';

    export default defineComponent({
        components: {
            VAppLayout,
            UploadFile,
        },
        methods: {
            uploadFile(form) {
                form.post(route('activity.store'));
            }
        }
    })
</script>
