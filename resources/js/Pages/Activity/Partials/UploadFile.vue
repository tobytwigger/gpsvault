<template>
    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
        <div class="mt-8 text-2xl">
            Upload an activity
        </div>

        <div class="mt-6 text-gray-500">

            <jet-validation-errors class="mb-4" />

            <form @submit.prevent="submit">

                <div>
                    <jet-label for="activity_name" value="Name" />
                    <jet-input id="activity_name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                </div>

                <div class="mt-4">
                    <jet-label for="file" value="Activity File" />
                    <jet-input id="file" type="file" @input="form.file = $event.target.files[0]" class="mt-1 block w-full" required />
                </div>

                <div class="mt-4">
                    <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Upload
                    </jet-button>
                </div>
            </form>

        </div>
    </div>
</template>


<script>
import { defineComponent } from 'vue'

import {useForm} from '@inertiajs/inertia-vue3'
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetButton from '@/Jetstream/Button.vue'

export default defineComponent({
    components: {
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors
    },
    name: "UploadFile",
    data() {
        return {
            form: useForm({
                name: null,
                file: null,
            })
        }
    },
    methods: {
        submit() {
            this.$emit('submitted', this.form);
        }
    }
})
</script>
