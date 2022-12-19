<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog"  v-bind:showing="showDialog">

        </slot>

        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    {{ title }}
                </v-card-title>
                <v-card-text>
                    {{ text }}
                </v-card-text>
                <v-card-text>
                    <v-form @submit.prevent="submit">
                        <v-text-field
                            id="title"
                            v-model="form.title"
                            label="Title"
                            hint="A name for the file"
                            name="title"
                            type="text"
                            :error="form.errors.hasOwnProperty('title')"
                            :error-messages="form.errors.hasOwnProperty('title') ? [form.errors.title] : []"
                        ></v-text-field>

                        <v-textarea
                            id="caption"
                            v-model="form.caption"
                            label="Caption"
                            hint="A caption/description for the file"
                            name="caption"
                            :error="form.errors.hasOwnProperty('caption')"
                            :error-messages="form.errors.hasOwnProperty('caption') ? [form.errors.caption] : []"
                        ></v-textarea>

                        <c-file-input
                            v-if="oldFile === null"
                            multiple
                            v-model="form.files"
                            id="files"
                            name="files"
                            label="Files"
                            hint="Upload any files to store with the route."
                            :error="form.errors.hasOwnProperty('files')"
                            :error-messages="form.errors.hasOwnProperty('files') ? [form.errors.files] : []"
                        ></c-file-input>
                    </v-form>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                        :disabled="form.processing"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                    >
                        Upload
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import CFileInput from '../CFileInput';
import modal from '../../mixins/modal';
export default {
    name: "CRouteFileFormDialog",
    components: {CFileInput},
    mixins: [modal],
    props: {
        routeModel: {
            required: true,
            type: Object
        },
        oldFile: {
            required: false,
            type: Object,
            default: null
        },
        title: {
            required: true,
            type: String
        },
        text: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                files: null,
                title: null,
                caption: null,
                _method: this.oldFile ? 'patch' : 'post'
            })
        }
    },
    mounted() {
        if(this.oldFile) {
            this.form.title = this.oldFile.title;
            this.form.caption = this.oldFile.caption;
        }
    },
    methods: {
        submit() {
            this.showDialog = false;
            this.form.post(
                this.oldFile
                    ? route('route.file.update', [this.routeModel.id, this.oldFile.id])
                    : route('route.file.store', [this.routeModel.id]),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.showDialog = false;
                    }
                });
        },
        triggerDialog() {
            this.showDialog = true;
        }
    }
}
</script>

<style scoped>

</style>
