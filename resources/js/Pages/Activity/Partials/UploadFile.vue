<template>
    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
        <div class="mt-8 text-2xl">
            Upload an activity
        </div>

        <div class="mt-6 text-gray-500">

            <jet-validation-errors class="mb-4"/>

            <form @submit.prevent="submit">

                <div>
                    <jet-label for="activity_name" value="Name"/>
                    <jet-input id="activity_name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus/>
                </div>

                <div class="mt-4">
                    <jet-label for="file" value="Activity File"/>
                    <jet-input id="file" type="file" @input="changeFiles" class="mt-1 block w-full" required/>
                </div>

                <div v-if="checkingDuplicate">
                    Checking for duplicates
                </div>
                <div v-else-if="duplicateActivity !== null">
                    This activity is a duplicate of
                    <Link :href="route('activity.show', duplicateActivity.id)">{{ duplicateActivity.name }}</Link>
                </div>

                <div class="mt-4">
                    <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Upload
                    </jet-button>
                </div>
            </form>
        </div>

        <jet-confirmation-modal :show="confirmingUploadDuplicate" @close="confirmingUploadDuplicate = false" v-if="duplicateActivity">
            <template #title>
                Upload duplicate activity?
            </template>

            <template #content>
                This activity is a duplicate of
                <Link :href="route('activity.show', duplicateActivity.id)">{{ duplicateActivity.name }}</Link>. Are you sure you still want to upload this activity?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingUploadDuplicate = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Upload Anyway
                </jet-button>
            </template>
        </jet-confirmation-modal>


    </div>
</template>


<script>
import {Link} from '@inertiajs/inertia-vue';
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import NavLink from '../../../Jetstream/NavLink';
import CryptoJS from 'crypto-js';

export default {
    components: {
        NavLink,
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors,
        Link,
        JetSecondaryButton,
        JetConfirmationModal
    },
    name: "UploadFile",
    data() {
        return {
            form: this.$inertia.form({
                name: null,
                file: null
            }),
            duplicateActivity: null,
            checkingDuplicate: false,
            confirmingUploadDuplicate: false
        }
    },
    methods: {
        changeFiles(event) {
            this.form.file = event.target.files[0];
            this.checkForDuplicateActivity();
        },
        checkForDuplicateActivity() {
            let reader = new FileReader();
            let self = this;
            reader.addEventListener('load',function (event) {
                self.checkingDuplicate = true;
                let hash = CryptoJS.MD5(CryptoJS.enc.Latin1.parse(event.target.result)).toString();
                console.log(hash);
                axios.post(route('activity.file.duplicate'), {hash: hash})
                    .then(response => self.duplicateActivity = response.data.activity)
                    .catch(error => self.duplicateActivity = null)
                    .then(() => self.checkingDuplicate = false);
            });
            reader.readAsBinaryString(this.form.file);
        },
        submit() {
            if(this.duplicateActivity && !this.confirmingUploadDuplicate) {
                this.confirmingUploadDuplicate = true;
            } else {
                this.confirmingUploadDuplicate = false;
                this.$emit('submitted', this.form);
            }
        }
    }
}
</script>
