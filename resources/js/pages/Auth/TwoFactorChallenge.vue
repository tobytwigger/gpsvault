<template>
    <c-auth-wrapper title="Two-factor Confirmation">
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title class="justify-center">
                    <span class="primary--text">Two Factor Authentication</span>
                </v-card-title>

                <v-card-text>
                    <template v-if="! recovery">
                        Please confirm access to your account by entering the authentication code provided by your authenticator application.
                    </template>

                    <template v-else>
                        Please confirm access to your account by entering one of your emergency recovery codes.
                    </template>

                    <v-otp-input
                        v-if="!recovery"
                        length="6"
                        id="code"
                        ref="code"
                        v-model="form.code"
                        label="One time code"
                        hint="A one time code from your authentication app."
                        name="code"
                        autocomplete="one-time-code"
                        :error="form.errors.hasOwnProperty('code')"
                        :error-messages="form.errors.hasOwnProperty('code') ? [form.errors.code] : []"
                    ></v-otp-input>

                    <v-text-field
                        v-else
                        id="recovery_code"
                        v-model="form.recovery_code"
                        label="Recovery Code"
                        hint="One of your recovery codes."
                        name="recovery_code"
                        ref="recovery_code"
                        type="text"
                        autocomplete="one-time-code"
                        :error="form.errors.hasOwnProperty('recovery_code')"
                        :error-messages="form.errors.hasOwnProperty('recovery_code') ? [form.errors.recovery_code] : []"
                    ></v-text-field>

                    <v-switch
                        v-model="recovery"
                        flat
                        :label="recoverySwitchLabel"
                    ></v-switch>

                </v-card-text>

                <v-card-actions>
                    <v-btn :loading="form.processing" :disabled="form.processing" aria-label="Log in" block color="primary"
                           type="submit">
                        <v-icon>mdi-arrow-right</v-icon>
                    </v-btn>
                </v-card-actions>
                <v-card-text>
                    <v-btn block @click="$inertia.visit(ziggyRoute('login'))" text>Back to Login</v-btn>
                </v-card-text>
            </v-card>
        </v-form>
    </c-auth-wrapper>
</template>

<script>
    import { Head } from '@inertiajs/inertia-vue';
    import CAuthWrapper from 'ui/layouts/CAuthWrapper';

    export default {
        components: {
            CAuthWrapper,
            Head
        },

        data() {
            return {
                recovery: false,
                form: this.$inertia.form({
                    code: '',
                    recovery_code: '',
                })
            }
        },

        watch: {
            recovery() {
                this.$nextTick(() => {
                    if (this.recovery) {
                        this.$refs.recovery_code.focus()
                        this.form.code = '';
                    } else {
                        this.$refs.code.focus()
                        this.form.recovery_code = ''
                    }
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.ziggyRoute('two-factor.login'))
            }
        },
        computed: {
            recoverySwitchLabel() {
                if(this.recovery) {
                    return 'Use a recovery code';
                }
                return 'Use an authentication code';
            }
        }
    }
</script>
