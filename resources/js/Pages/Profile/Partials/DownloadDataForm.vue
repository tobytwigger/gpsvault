<template>
    <jet-action-section>
        <template #title>
            Download Data
        </template>

        <template #description>
            Download all the data you've uploaded to this site in a zip file.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                You can download a single zip file of all the data you've uploaded to this site by clicking Download. We will send you an email when your file is ready to download.
            </div>

            <div class="mt-5">
                <jet-button @click="downloadData" :class="{ 'opacity-25': generatingZip }" :disabled="generatingZip">
                    <span v-if="generatingZip">Generating Zip</span><span v-else>Download Data</span>
                </jet-button>
            </div>

            <jet-action-message :on="generatingZip && !form.processing" class="mr-3">
                You should receive an email shortly with a link to download your data.
            </jet-action-message>

        </template>
    </jet-action-section>
</template>

<script>
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetActionSection from '@/Jetstream/ActionSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import moment from 'moment';

export default {
    components: {
        JetActionMessage,
        JetButton,
        JetActionSection,
        JetInput,
        JetInputError,
        JetLabel,
    },
    data() {
        return {
            form: this.$inertia.form({}),
            generateUntil: null
        }
    },

    computed: {
        generatingZip() {
            return this.form.processing === true ? this.form.processing : (this.generateUntil && moment().isBefore(this.generateUntil))
        }
    },

    methods: {
        downloadData() {
            this.form.post(route('data.download'), {
                onSuccess: () => this.generateUntil = moment().add(1, 'minute').toISOString()
            })
        },
    },
}
</script>
