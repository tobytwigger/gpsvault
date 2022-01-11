<template>
    <c-auth-wrapper title="Register">
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title class="justify-center">
                    <span class="primary--text">Register</span>
                </v-card-title>

                <v-card-text>
                    <v-text-field
                        id="name"
                        v-model="form.name"
                        label="Name"
                        hint="Your name or a username."
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

                    <v-text-field
                        id="password"
                        v-model="form.password"
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
                        hint="Enter your password again."
                        name="password_confirmation"
                        prepend-icon="mdi-lock"
                        type="password"
                        :error="form.errors.hasOwnProperty('password_confirmation')"
                        :error-messages="form.errors.hasOwnProperty('password_confirmation') ? [form.errors.password_confirmation] : []"
                    ></v-text-field>

                </v-card-text>
                <v-card-actions>
                    <v-btn :loading="form.processing" :disabled="form.processing" aria-label="Register" block color="primary"
                           type="submit">
                        <v-icon>mdi-arrow-right</v-icon>
                    </v-btn>
                </v-card-actions>
                <v-card-text>
                    <v-btn block @click="$inertia.visit(ziggyRoute('login'))" text>I already have an account</v-btn>
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
        canResetPassword: Boolean
    },

    data() {
        return {
            form: this.$inertia.form({
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            })
        }
    },

    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ...data,
                    remember: this.form.remember ? 'on' : ''
                }))
                .post(this.ziggyRoute('register'), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                })
        }
    }
}
</script>
