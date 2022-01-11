<template>
    <c-confirmation-dialog :title="title" :button-text="buttonText" :loading="loading" :cancel-button-text="cancelButtonText" @confirm="confirmPassword" @showing="startConfirmingPassword">
        <template v-slot:activator="{trigger,showing}">
            <slot name="activator" v-bind:trigger="trigger" v-bind:showing="showing"></slot>
        </template>
        Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.

        <v-form @submit.prevent="confirmPassword">
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
</template>

<script>
import CConfirmationDialog from './CConfirmationDialog';
export default {
    name: "CConfirmsPasswordDialog",
    components: {CConfirmationDialog},
    props: {
        title: {
            required: true,
            type: String
        },
        buttonText: {
            required: true,
            type: String
        },
        loading: {
            required: false,
            type: Boolean,
            default: false
        },
        cancelButtonText: {
            required: false,
            type: String,
            default: 'Cancel'
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                password: ''
            }),
        }
    },
    methods: {
        startConfirmingPassword() {
            axios.get(this.ziggyRoute('password.confirmation')).then(response => {
                if (response.data.confirmed) {
                    this.$emit('confirmed');
                } else {
                    setTimeout(() => this.$refs.password.focus(), 250)
                }
            })
        },

        confirmPassword() {
            this.form.post(this.ziggyRoute('password.confirm'), {
                errorBag: 'confirmPassword',
                onSuccess: () => this.$emit('confirmed'),
                onError: () => this.$refs.password.focus()
            })
        },
    }
}
</script>

<style scoped>

</style>
