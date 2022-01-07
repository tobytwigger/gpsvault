<template>
    <div>

        <div class="flex flex-row w-full flex-wrap">

            <div class="flex-grow px-4" v-if="images.length > 0">
<!--                <carousel :items-to-show="3.95" :wrap-around="true">-->
<!--                    <slide v-for="image in images" :key="image.id" class="p-2">-->
<!--                        <div class="carousel__item cursor-pointer" @click="viewingImage = image">-->
<!--                            <img-->
<!--                                :src="route('file.preview', image.id)"-->
<!--                                :alt="image.caption ?? 'Activity media'"-->
<!--                                class="shadow-lg rounded max-w-full h-auto align-middle border-none"/>-->
<!--                        </div>-->
<!--                    </slide>-->

<!--                    <template #addons>-->
<!--                        <navigation />-->
<!--                        <pagination />-->
<!--                    </template>-->
<!--                </carousel>-->
            </div>

            <div class="flex-grow">
                <div class="pl-3 pr-4 py-3 flex-shrink text-right text-sm flex-1">
                    <jet-button href="#" @click.native="uploadingMedia = true" :disabled="uploadingMedia === true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="mr-1">Upload a File</span>
                    </jet-button>
                </div>

                <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                    <li class="pl-3 pr-4 py-3 flex items-center justify-between flex-wrap flex-row text-sm" v-for="file in files">
                        <div class="flex flex-1 items-center">
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

            </div>
        </div>

<!--        <vue-easy-lightbox-->
<!--            scrollDisabled-->
<!--            escDisabled-->
<!--            moveDisabled-->
<!--            :imgs="lightboxImages"-->
<!--            :index="viewingImageIndex"-->
<!--            :visible="viewingImage && viewingImage.hasOwnProperty('id')"-->
<!--            @hide="viewingImage = null"-->
<!--        ></vue-easy-lightbox>-->

        <modal :show="uploadingMedia" :closeable="true" @close="uploadingMedia = false">
            <form @submit.prevent="uploadFiles">

                <div class="px-6 py-4">
                    <div class="text-lg">
                        Upload file for Activity
                    </div>

                    <div class="mt-4">
                        <p>Upload files or media associated with the activity</p>

                        <div class="mt-4">
                            <jet-label for="files" value="Files"/>
                            <jet-input id="files" type="file" ref="filesUpload" multiple @input="form.files = $event.target.files"
                                       class="mt-1 block w-full" required/>
                        </div>

                    </div>
                </div>

                <div class="px-12 py-4 bg-gray-100 text-right">
                    <div>
                        <jet-button class="ml-4" :disabled="form.processing" type="submit">
                            Store Files
                        </jet-button>
                        <button type="reset" @click.prevent="resetForm" class="ml-4 inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </modal>

        <edit-file :file="fileToEdit" :activity-id="activity.id" @close="fileToEdit = null">

        </edit-file>
    </div>
</template>

<script>
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetButton from '@/Jetstream/Button.vue'
import EditFile from './EditFile';
import Modal from '@/Jetstream/Modal';

export default {
    name: "FileManager",
    components: {
        EditFile,
        Modal,
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors,
        // Carousel,
        // Slide,
        // Pagination,
        // Navigation,
    },
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                files: null,
                _method: 'patch'
            }),
            uploadingMedia: false,
            viewingImage: false,
            fileToEdit: null
        }
    },
    computed: {
        viewingImageIndex() {
            if(this.viewingImage && this.viewingImage.hasOwnProperty('id')) {
                return this.images.indexOf(this.viewingImage);
            }
            return 0;
        },
        files() {
            return this.activity.files
        },
        images() {
            return this.files.filter(file => file.mimetype.startsWith('image/'));
        },
        lightboxImages() {
            return this.images.map(file => {
                return {
                    title: file.caption,
                    src: route('file.preview', file.id)
                }
            })
        }
    },
    methods: {
        resetForm() {
            this.uploadingMedia = false;
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
.carousel__slide > .carousel__item {
    transform: scale(1);
    opacity: 0.5;
    transition: 0.5s;
}
.carousel__slide--visible > .carousel__item {
    opacity: 1;
    transform: rotateY(0);
}
.carousel__slide--next > .carousel__item {
    transform: scale(0.9) translate(-10px);
}
.carousel__slide--prev > .carousel__item {
    transform: scale(0.9) translate(10px);
}
.carousel__slide--active > .carousel__item {
    transform: scale(1.1);
}
</style>
