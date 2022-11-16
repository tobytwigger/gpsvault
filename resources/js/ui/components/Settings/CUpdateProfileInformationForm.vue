<template>
    <c-form-section title="Profile Information" description="Update your account's profile information and email address.">
        <v-btn @click="triggerDialog" :disabled="showDialog">Edit your profile information</v-btn>
        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    Edit your profile information
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="submit">

                        <v-text-field
                            id="name"
                            v-model="form.name"
                            label="name"
                            hint="Your name address."
                            name="name"
                            prepend-icon="mdi-account"
                            type="text"
                            autocomplete="name"
                            :error="form.errors.hasOwnProperty('name')"
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                        ></v-text-field>

                        <v-text-field
                            id="email"
                            v-model="form.email"
                            label="Email"
                            hint="Your email address."
                            name="email"
                            prepend-icon="mdi-account"
                            type="text"
                            autocomplete="email"
                            :error="form.errors.hasOwnProperty('email')"
                            :error-messages="form.errors.hasOwnProperty('email') ? [form.errors.email] : []"
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                    >
                        Update information
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </c-form-section>
</template>

<script>
import CFormSection from './CFormSection';

export default {
    name: 'CUpdateProfileInformationForm',
    components: {
        CFormSection,
    },

    data() {
        return {
            form: this.$inertia.form({
                _method: 'PUT',
                name: this.$page.props.user.name,
                email: this.$page.props.user.email,
                photo: null,
            }),
            showDialog: false,
            photoPreview: null,
        }
    },

    methods: {
        triggerDialog() {
            this.showDialog = true;
        },
        close() {
            this.showDialog = false;
        },
        submit() {
            if (this.$refs.photo) {
                this.form.photo = this.$refs.photo.files[0]
            }

            this.form.post(route('user-profile-information.update'), {
                errorBag: 'updateProfileInformation',
                preserveScroll: true,
                onSuccess: () => {
                    this.clearPhotoFileInput();
                    this.close();
                },
            });
        },

        selectNewPhoto() {
            this.$refs.photo.click();
        },

        updatePhotoPreview() {
            const photo = this.$refs.photo.files[0];

            if (!photo) return;

            const reader = new FileReader();

            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };

            reader.readAsDataURL(photo);
        },

        deletePhoto() {
            this.$inertia.delete(route('current-user-photo.destroy'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.photoPreview = null;
                    this.clearPhotoFileInput();
                },
            });
        },

        clearPhotoFileInput() {
            if (this.$refs.photo?.value) {
                this.$refs.photo.value = null;
            }
        },
    },
}
</script>
