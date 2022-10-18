<template>
    <c-form-section description="Ensure your account is using a long, random password to stay secure."
                    title="Update Password">
        <v-btn @click="triggerDialog" :disabled="showDialog">Change Password</v-btn>
        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    Change Password
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="updatePassword">
                        <v-text-field
                            id="current_password"
                            ref="current_password"
                            v-model="form.current_password"
                            :error="form.errors.hasOwnProperty('current_password')"
                            :error-messages="form.errors.hasOwnProperty('current_password') ? [form.errors.current_password] : []"
                            autocomplete="current-password"
                            hint="The password you use to log in currently."
                            label="Current password"
                            name="current_password"
                            prepend-icon="mdi-lock"
                            type="password"
                        ></v-text-field>

                        <v-text-field
                            id="password"
                            ref="password"
                            v-model="form.password"
                            :error="form.errors.hasOwnProperty('password')"
                            :error-messages="form.errors.hasOwnProperty('password') ? [form.errors.password] : []"
                            autocomplete="new-password"
                            hint="A new password to use for your account."
                            label="New password"
                            name="password"
                            prepend-icon="mdi-lock"
                            type="password"
                        ></v-text-field>

                        <v-text-field
                            id="password_confirmation"
                            ref="password_confirmation"
                            v-model="form.password_confirmation"
                            :error="form.errors.hasOwnProperty('password_confirmation')"
                            :error-messages="form.errors.hasOwnProperty('password_confirmation') ? [form.errors.password_confirmation] : []"
                            autocomplete="new-password"
                            hint="Repeat your new password to make sure you haven't made any mistakes."
                            label="New password (again)"
                            name="password_confirmation"
                            prepend-icon="mdi-lock"
                            type="password"
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
                        @click="updatePassword"
                        :loading="form.processing"
                        :disabled="form.processing"
                    >
                        Update password
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </c-form-section>
</template>

<script>
import CFormSection from './CFormSection';

export default {
    name: 'CUpdatePasswordForm',
    components: {
        CFormSection
    },

    data() {
        return {
            form: this.$inertia.form({
                current_password: '',
                password: '',
                password_confirmation: '',
            }),
            showDialog: false
        }
    },

    methods: {
        triggerDialog() {
            this.showDialog = true;
        },
        close() {
            this.showDialog = false;
        },
        updatePassword() {
            this.form.put(route('user-password.update'), {
                errorBag: 'updatePassword',
                preserveScroll: true,
                onSuccess: () => {
                    this.form.reset();
                    this.close();
                },
                onError: () => {
                    if (this.form.errors.password) {
                        this.form.reset('password', 'password_confirmation')
                        this.$refs.password.focus()
                    }

                    if (this.form.errors.current_password) {
                        this.form.reset('current_password')
                        this.$refs.current_password.focus()
                    }
                }
            })
        },
    },
}
</script>
