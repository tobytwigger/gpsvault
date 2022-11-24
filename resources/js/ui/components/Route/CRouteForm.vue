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
                    <v-form @submit.prevent="submit">
                        <v-text-field
                            id="name"
                            v-model="form.name"
                            required
                            label="Name"
                            hint="A name for the route"
                            name="name"
                            type="text"
                            :error="form.errors.hasOwnProperty('name')"
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                        ></v-text-field>

                        <v-textarea
                            v-if="oldRoute !== null"
                            id="description"
                            v-model="form.description"
                            label="Description"
                            hint="A description for the route"
                            name="description"
                            :error="form.errors.hasOwnProperty('description')"
                            :error-messages="form.errors.hasOwnProperty('description') ? [form.errors.description] : []"
                        ></v-textarea>

                        <v-textarea
                            v-if="oldRoute !== null"
                            id="notes"
                            v-model="form.notes"
                            label="Notes"
                            hint="Notes for the route"
                            name="notes"
                            :error="form.errors.hasOwnProperty('notes')"
                            :error-messages="form.errors.hasOwnProperty('notes') ? [form.errors.notes] : []"
                        ></v-textarea>

                        <c-file-input
                            v-model="form.file"
                            v-if="oldRoute === null"
                            id="file"
                            name="file"
                            label="Route File"
                            :hint="fileInputDescription"
                            :error="form.errors.hasOwnProperty('file')"
                            :error-messages="fileErrorMessages"
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
                        {{ buttonText }}
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
    name: "CRouteForm",
    components: {CFileInput},
    mixins: [modal],
    props: {
        oldRoute: {
            required: false,
            type: Object,
            default: null
        },
        title: {
            required: true,
            type: String
        },
        buttonText: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                name: null,
                description: null,
                notes: null,
                file: null,
                _method: this.oldRoute ? 'patch' : 'post'
            })
        }
    },
    mounted() {
        this.updateFromOldRoute();
    },
    computed: {
        file() {
            return this.form.file;
        },
        fileErrorMessages() {
            return this.form.errors.hasOwnProperty('file') ? [this.form.errors.file] : []
        },
        fileInputDescription() {
            if(this.oldRoute) {
                return 'Upload a new gpx file for the route.';
            }
            return 'Upload the gpx file for the route.';
        }
    },
    methods: {
        updateFromOldRoute() {
            if(this.oldRoute) {
                this.form.name = this.oldRoute.name;
                this.form.description = this.oldRoute.description;
                this.form.notes = this.oldRoute.notes;
            }
        },
        submit() {
            this.form.post(
                this.oldRoute
                    ? route('route.update', this.oldRoute.id)
                    : route('route.store'),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.updateFromOldRoute();
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
