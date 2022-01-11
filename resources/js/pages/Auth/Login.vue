<template>
    <c-auth-wrapper title="Login">
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title class="justify-center">
                    <span class="primary--text">Sign In</span>
                </v-card-title>

                <v-card-text>
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
                        hint="Your password."
                        name="password"
                        prepend-icon="mdi-lock"
                        type="password"
                        :error="form.errors.hasOwnProperty('password')"
                        :error-messages="form.errors.hasOwnProperty('password') ? [form.errors.password] : []"
                    ></v-text-field>

                    <v-checkbox
                        id="remember"
                        v-model="form.remember"
                        class="ma-0"
                        hint="Would you like to log in automatically next time?"
                        label="Remember Me"
                        name="remember"
                        :error="form.errors.hasOwnProperty('remember')"
                        :error-messages="form.errors.hasOwnProperty('remember') ? [form.errors.remember] : []"
                    ></v-checkbox>
                </v-card-text>
                <v-card-actions>
                    <v-btn :loading="form.processing" :disabled="form.processing" aria-label="Login" block color="primary"
                           type="submit">
                        <v-icon>mdi-arrow-right</v-icon>
                    </v-btn>
                </v-card-actions>
                <v-card-text>
                    <v-btn block @click="$inertia.visit(ziggyRoute('register'))" text>Create an account</v-btn>
                    <v-btn block v-if="canResetPassword" @click="$inertia.visit(ziggyRoute('password.request'))" text>I've forgotten my password</v-btn>
                </v-card-text>
            </v-card>
        </v-form>
    </c-auth-wrapper>
</template>

<script>
import CAuthWrapper from 'ui/layouts/CAuthWrapper';

export default {
    name: 'Login',
    components: {
        CAuthWrapper
    },

    props: {
        canResetPassword: Boolean
    },

    data() {
        return {
            form: this.$inertia.form({
                email: '',
                password: '',
                remember: false
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
                .post(this.ziggyRoute('login'), {
                    onFinish: () => this.form.reset('password'),
                })
        }
    }
}
</script>
