<template>
    <c-auth-wrapper title="Forgot Password">
        <template #alerts>
            <v-alert v-if="status" outlined type="success" text>
                {{status}}
            </v-alert>
        </template>

        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title class="justify-center">
                    <span class="primary--text">Request a new password</span>
                </v-card-title>

                <v-card-text>
                    <v-text-field
                        id="email"
                        v-model="form.email"
                        label="Email"
                        hint="The email address you use for your account."
                        name="email"
                        prepend-icon="mdi-account"
                        type="text"
                        autocomplete="email"
                        :error="form.errors.hasOwnProperty('email')"
                        :error-messages="form.errors.hasOwnProperty('email') ? [form.errors.email] : []"
                    ></v-text-field>

                </v-card-text>
                <v-card-actions>
                    <v-btn :loading="form.processing" :disabled="form.processing" aria-label="Request Email" block color="primary"
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
    name: 'ForgotPassword',
    components: {
        CAuthWrapper
    },

    props: {
        status: String
    },

    data() {
        return {
            form: this.$inertia.form({
                email: ''
            })
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('password.email'), {
                onFinish: () => {
                    this.form.reset('email');

                }
            });
        }
    }
}
</script>
