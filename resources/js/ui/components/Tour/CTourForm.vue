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
                            hint="A name for the tour"
                            name="name"
                            type="text"
                            :error="form.errors.hasOwnProperty('name')"
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                        ></v-text-field>

                        <v-textarea
                            v-if="oldTour !== null"
                            id="description"
                            v-model="form.description"
                            label="Description"
                            hint="A description for the tour"
                            name="description"
                            :error="form.errors.hasOwnProperty('description')"
                            :error-messages="form.errors.hasOwnProperty('description') ? [form.errors.description] : []"
                        ></v-textarea>

                        <v-textarea
                            v-if="oldTour !== null"
                            id="notes"
                            v-model="form.notes"
                            label="Notes"
                            hint="A notes for the tour"
                            name="notes"
                            :error="form.errors.hasOwnProperty('notes')"
                            :error-messages="form.errors.hasOwnProperty('notes') ? [form.errors.notes] : []"
                        ></v-textarea>
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
import modal from '../../mixins/modal';

export default {
    name: "CTourForm",
    mixins: [modal],
    props: {
        oldTour: {
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
                _method: this.oldTour ? 'patch' : 'post'
            })
        }
    },
    mounted() {
        this.updateFromOldTour();
    },
    computed: {
        file() {
            return this.form.file;
        },
    },
    methods: {
        updateFromOldTour() {
            if(this.oldTour) {
                this.form.name = this.oldTour.name;
                this.form.description = this.oldTour.description;
                this.form.notes = this.oldTour.notes;
            }
        },
        submit() {
            this.form.post(
                this.oldTour
                    ? route('tour.update', this.oldTour.id)
                    : route('tour.store'),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.updateFromOldTour();
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
