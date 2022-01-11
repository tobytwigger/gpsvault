<template>
    <c-form-section title="Delete Account" description="Permanently delete your account.">
        <c-confirmation-dialog button-text="Delete Account" :loading="form.processing" title="Delete Account?" @confirm="deleteUser">
            <template v-slot:activator="{trigger,showing}">
                <v-btn icon @click.stop="trigger" :loading="form.processing" :disabled="showing">
                    <v-icon>mdi-delete</v-icon>
                    Delete Account
                </v-btn>
            </template>
            Once your account is deleted, all of its resources and data will be permanently deleted.

            Before deleting your account, please download any data or information that you wish to retain.

            <v-form @submit.prevent="deleteUser">
                <v-text-field
                    id="password"
                    v-model="form.password"
                    label="Password"
                    hint="Your password."
                    name="password"
                    ref="password"
                    prepend-icon="mdi-lock"
                    type="password"
                    :error="form.errors.hasOwnProperty('password')"
                    :error-messages="form.errors.hasOwnProperty('password') ? [form.errors.password] : []"
                ></v-text-field>
            </v-form>

        </c-confirmation-dialog>
    </c-form-section>
</template>

<script>
    import CFormSection from './CFormSection';
    import CConfirmationDialog from '../CConfirmationDialog';

    export default {
        name: 'CDeleteUserForm',
        components: {
            CConfirmationDialog,
            CFormSection
        },

        data() {
            return {
                form: this.$inertia.form({
                    password: '',
                })
            }
        },

        methods: {
            deleteUser() {
                this.form.delete(ziggyRoute('current-user.destroy'), {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                    onError: () => this.$refs.password.focus(),
                    onFinish: () => this.form.reset(),
                })
            },
        },
    }
</script>
