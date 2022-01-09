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
                            label="Name"
                            hint="A name for the route"
                            name="name"
                            type="text"
                            :error="form.errors.hasOwnProperty('name')"
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                        ></v-text-field>

                        <v-textarea
                            id="description"
                            v-model="form.description"
                            label="Description"
                            hint="A description for the route"
                            name="description"
                            :error="form.errors.hasOwnProperty('description')"
                            :error-messages="form.errors.hasOwnProperty('description') ? [form.errors.description] : []"
                        ></v-textarea>

                        <v-file-input
                            v-if="oldRoute === null"
                            show-size
                            truncate-length="30"
                            v-model="form.file"
                            id="file"
                            :loading="checkingDuplicate"
                            name="file"
                            @change="checkForDuplicateRoute"
                            label="Route File"
                            hint="Upload the gpx file for the route."
                            :error="form.errors.hasOwnProperty('file')"
                            :error-messages="fileErrorMessages"
                        ></v-file-input>
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
import duplicateRouteChecker from '../../mixins/duplicateRouteChecker';

export default {
    name: "CRouteForm",
    mixins: [duplicateRouteChecker],
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
            showDialog: false,
            form: this.$inertia.form({
                name: null,
                description: null,
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
            if(this.duplicateMessage) {
                return [this.duplicateMessage];
            }
            return this.form.errors.hasOwnProperty('file') ? [this.form.errors.file] : []
        }
    },
    methods: {
        updateFromOldRoute() {
            if(this.oldRoute) {
                this.form.name = this.oldRoute.name;
                this.form.description = this.oldRoute.description;
            }
        },
        submit() {
            this.form.post(
                this.oldRoute
                    ? route('route.update', this.oldRoute.id)
                    : route('route.store'),
                {
                    onSuccess: () => {
                        this.duplicateRoute = null;
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
