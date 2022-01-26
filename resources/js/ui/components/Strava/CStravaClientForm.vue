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
                            v-if="!oldClient"
                            label="Name"
                            hint="A name for the client, to help you identify it later"
                            name="name"
                            type="text"
                            :error="form.errors.hasOwnProperty('name')"
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                        ></v-text-field>

                        <v-text-field
                            id="client_id"
                            v-model="form.client_id"
                            v-if="!oldClient"
                            label="Client ID"
                            hint="The ID of the client, as given on Strava."
                            name="client_id"
                            type="text"
                            :error="form.errors.hasOwnProperty('client_id')"
                            :error-messages="form.errors.hasOwnProperty('client_id') ? [form.errors.client_id] : []"
                        ></v-text-field>

                        <v-text-field
                            id="client_secret"
                            v-model="form.client_secret"
                            label="Client Secret"
                            hint="The secret of the client, as given on Strava."
                            name="client_secret"
                            type="text"
                            :error="form.errors.hasOwnProperty('client_secret')"
                            :error-messages="form.errors.hasOwnProperty('client_secret') ? [form.errors.client_secret] : []"
                        ></v-text-field>

                        <v-textarea
                            id="description"
                            v-model="form.description"
                            label="Description"
                            hint="A description for the client."
                            name="description"
                            :error="form.errors.hasOwnProperty('description')"
                            :error-messages="form.errors.hasOwnProperty('description') ? [form.errors.description] : []"
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
export default {
    name: "CStravaClientForm",
    props: {
        oldClient: {
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
                client_id: null,
                client_secret: null,
                name: null,
                description: null,
                _method: this.oldClient ? 'patch' : 'post'
            })
        }
    },
    mounted() {
        this.updateFromOldClient();
    },
    methods: {
        updateFromOldClient() {
            if(this.oldClient) {
                this.form.client_id = this.oldClient.name;
                this.form.client_secret = this.oldClient.description;
                this.form.name = this.oldClient.name;
                this.form.description = this.oldClient.description;
            }
        },
        submit() {
            this.form.post(
                this.oldClient
                    ? route('strava.client.update', this.oldClient.id)
                    : route('strava.client.store'),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.updateFromOldClient();
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
