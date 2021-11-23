<template>
    <div v-if="count">
        <a href="#" @click.prevent="show = true" class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
            <div>Fix {{count}} uploads</div>
            <div class="ml-1 text-indigo-500">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </div>
        </a>
        <modal :show="show" :closeable="true" @close="show = false">
            <form @submit.prevent="upload">

                <div class="px-6 py-4">
                    <div class="text-lg">
                        Fix Strava Uploads
                    </div>

                    <div class="mt-4">
                        <p>We can't automatically import the gps files when synchronising from Strava. Fortunately, you can quickly attach all these files at once.</p>

                        <p class="text-sm mt-4">Follow the documentation to get your zip file from Strava, then upload it below.</p>

                        <div class="mt-4">
                            <jet-label for="file" value="Zip file" />
                            <jet-input id="file" type="file" @input="form.file = $event.target.files[0]" class="mt-1 block w-full" required />
                        </div>

                    </div>
                </div>

                <div class="px-12 py-4 bg-gray-100 text-right">
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Fix activities
                        </button>
                    </div>
                </div>
            </form>
        </modal>
    </div>
</template>

<script>
import Modal from '@/Jetstream/Modal';
import {useForm} from '@inertiajs/inertia-vue3';
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetButton from '@/Jetstream/Button.vue'

export default {
    name: "StravaAddon",
    components: {
        Modal,
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors
    },
    data() {
        return {
            show: false,
            form: useForm({
                file: null
            })
        }
    },
    props: {
        count: {
            required: false,
            type: Number,
            default: 0
        }
    },
    methods: {
        upload() {
            this.form.post(route('strava.fix'), {
                onSuccess: () => this.show = false
            });
        }
    }
}
</script>

<style scoped>

</style>
