<template>
    <c-form-section title="Profile Information" description="Update your account's profile information and email address.">
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

        <template #actions>
            <v-btn :loading="form.processing" :disabled="form.processing" @click="submit">
                Save
            </v-btn>
        </template>
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

                photoPreview: null,
            }
        },

        methods: {
            submit() {
                if (this.$refs.photo) {
                    this.form.photo = this.$refs.photo.files[0]
                }

                this.form.post(ziggyRoute('user-profile-information.update'), {
                    errorBag: 'updateProfileInformation',
                    preserveScroll: true,
                    onSuccess: () => (this.clearPhotoFileInput()),
                });
            },

            selectNewPhoto() {
                this.$refs.photo.click();
            },

            updatePhotoPreview() {
                const photo = this.$refs.photo.files[0];

                if (! photo) return;

                const reader = new FileReader();

                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };

                reader.readAsDataURL(photo);
            },

            deletePhoto() {
                this.$inertia.delete(ziggyRoute('current-user-photo.destroy'), {
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
