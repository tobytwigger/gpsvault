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
                            v-model="form.title"
                            label="Name"
                            hint="A name for the backup"
                            name="name"
                            type="text"
                            :error="form.errors.hasOwnProperty('title')"
                            :error-messages="form.errors.hasOwnProperty('title') ? [form.errors.title] : []"
                        ></v-text-field>

                        <v-textarea
                            id="description"
                            v-model="form.caption"
                            label="Description"
                            hint="A description for the backup"
                            name="description"
                            :error="form.errors.hasOwnProperty('caption')"
                            :error-messages="form.errors.hasOwnProperty('caption') ? [form.errors.caption] : []"
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
import moment from 'moment';

export default {
    name: "CBackupForm",
    props: {
        oldBackup: {
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
                title: null,
                caption: null,
                _method: this.oldBackup ? 'patch' : 'post'
            })
        }
    },
    mounted() {
        this.updateFromOldBackup();
    },
    methods: {
        updateFromOldBackup() {
            if(this.oldBackup) {
                this.form.title = this.oldBackup.title;
                this.form.caption = this.oldBackup.caption;
            } else {
                this.form.title = 'Full backup ' + moment().format('DD/MM/YYYY')
                this.form.caption = 'Backup taken at ' + moment().format('Mo MMM YYYY')
            }
        },
        submit() {
            this.form.post(
                this.oldBackup
                    ? route('backups.update', this.oldBackup.id)
                    : route('backups.store'),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.updateFromOldBackup();
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
