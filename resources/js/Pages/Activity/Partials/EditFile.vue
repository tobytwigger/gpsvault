<template>
    <modal :show="shouldShow" :closeable="true" @close="$emit('close')">
        <form @submit.prevent="editFile">

            <div class="px-6 py-4">
                <div class="text-lg">
                    Edit File
                </div>

                <div class="mt-4">
                    <p>Change the name and caption of a file</p>

                    <div class="mt-4">
                        <jet-label for="title" value="Title" />
                        <jet-input id="title" type="text" v-model="form.title" class="mt-1 block w-full" />
                    </div>

                    <div class="mt-4">
                        <jet-label for="caption" value="Caption" />
                        <textarea id="caption" v-model="form.caption" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                    </div>

                </div>
            </div>

            <div class="px-12 py-4 bg-gray-100 text-right">
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        Update Activity
                    </button>
                </div>
            </div>
        </form>
    </modal>
</template>

<script>
import Modal from '@/Jetstream/Modal';
import {useForm} from '@inertiajs/inertia-vue3';
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetButton from '@/Jetstream/Button.vue'

export default {
    name: "EditFile",
    components: {
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors,
        Modal
    },
    props: {
        file: {
            required: false,
            type: Object,
            default: null
        },
        activityId: {
            required: true
        }
    },
    data() {
        return {
            form: useForm({
                title: null,
                caption: null,
                _method: 'patch'
            })
        }
    },
    watch: {
        file: {
            handler: function () {
                this.updateDefaults()
            },
            deep: true
        }
    },
    methods: {
        updateDefaults() {
            if(this.file) {
                this.form.title = this.file.title ?? null;
                this.form.caption = this.file.caption ?? null;
            }
        },
        editFile() {
            this.form.post(route('activity.file.update', [this.activityId, this.file.id]), {
                onSuccess: () => {
                    this.form.reset();
                    this.$emit('close');
                }
            });
        }
    },
    computed: {
        shouldShow() {
            return this.file !== null;
        }
    },
    mounted() {
        this.updateDefaults();
    }
}
</script>

<style scoped>

</style>
