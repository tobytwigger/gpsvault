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
                    Edit activity
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="submit">
                        <v-text-field
                            id="name"
                            v-model="form.name"
                            label="Name"
                            hint="A name for the activity"
                            name="name"
                            type="text"
                            :error="form.errors.hasOwnProperty('name')"
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                        ></v-text-field>

                        <v-textarea
                            id="description"
                            v-model="form.description"
                            label="Description"
                            hint="A description for the activity"
                            name="description"
                            :error="form.errors.hasOwnProperty('description')"
                            :error-messages="form.errors.hasOwnProperty('description') ? [form.errors.description] : []"
                        ></v-textarea>

                        <v-file-input
                            v-if="oldActivity === null"
                            show-size
                            truncate-length="30"
                            v-model="form.files"
                            id="file"
                            name="file"
                            label="Activity File"
                            hint="Upload the gpx/tcx/fit file recording of your ride."
                            :error="form.errors.hasOwnProperty('file')"
                            :error-messages="form.errors.hasOwnProperty('file') ? [form.errors.file] : []"
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
                        Update
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "CActivityForm",
    props: {
        oldActivity: {
            required: false,
            type: Object
        }
    },
    data() {
        return {
            showDialog: false,
            form: this.$inertia.form({
                name: null,
                description: null,
                file: null,
                _method: this.oldActivity ? 'patch' : 'post'
            })
        }
    },
    mounted() {
        if(this.oldActivity) {
            this.form.name = this.oldActivity.name;
            this.form.description = this.oldActivity.description;
        }
    },
    methods: {
        submit() {
            this.form.post(
                this.oldActivity
                    ? route('activity.update', this.oldActivity.id)
                    : route('activity.store'),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.form.name = this.oldActivity.name;
                        this.form.description = this.oldActivity.description;
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
