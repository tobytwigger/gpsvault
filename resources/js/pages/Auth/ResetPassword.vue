<template>
    <c-auth-wrapper title="Reset Password">
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title class="justify-center">
                    <span class="primary--text">Reset your password</span>
                </v-card-title>

                <v-card-text>

                    <v-text-field
                        id="email"
                        v-model="form.email"
                        label="Email"
                        hint="Your email address."
                        disabled
                        name="email"
                        prepend-icon="mdi-account"
                        type="email"
                        autocomplete="email"
                        :error="form.errors.hasOwnProperty('email')"
                        :error-messages="form.errors.hasOwnProperty('email') ? [form.errors.email] : []"
                    ></v-text-field>

                    <v-text-field
                        id="password"
                        v-model="form.password"
                        autocomplete="new-password"
                        label="Password"
                        hint="A secure password."
                        name="password"
                        prepend-icon="mdi-lock"
                        type="password"
                        :error="form.errors.hasOwnProperty('password')"
                        :error-messages="form.errors.hasOwnProperty('password') ? [form.errors.password] : []"
                    ></v-text-field>

                    <v-text-field
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        label="Confirm your password"
                        autocomplete="new-password"
                        hint="Enter your password again."
                        name="password_confirmation"
                        prepend-icon="mdi-lock"
                        type="password"
                        :error="form.errors.hasOwnProperty('password_confirmation')"
                        :error-messages="form.errors.hasOwnProperty('password_confirmation') ? [form.errors.password_confirmation] : []"
                    ></v-text-field>

                </v-card-text>
                <v-card-actions>
                    <v-btn :loading="form.processing" :disabled="form.processing" aria-label="Reset Password" block color="primary"
                           type="submit">
                        <v-icon>mdi-arrow-right</v-icon>
                    </v-btn>
                </v-card-actions>
                <v-card-text>
                    <v-btn block @click="$inertia.visit(route('login'))" text>Back to Login</v-btn>
                </v-card-text>
            </v-card>
        </v-form>
    </c-auth-wrapper>
</template>

<script>
import CAuthWrapper from 'ui/layouts/CAuthWrapper';

export default {
    name: 'Register',
    components: {
        CAuthWrapper
    },

    props: {
        email: String,
        token: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                token: this.token,
                email: this.email,
                password: '',
                password_confirmation: '',
            })
        }
    },

    methods: {
        submit() {
            this.form.post(route('password.update'), {
                onFinish: () => this.form.reset('password', 'password_confirmation'),
            })
        }
    }
}
</script>
