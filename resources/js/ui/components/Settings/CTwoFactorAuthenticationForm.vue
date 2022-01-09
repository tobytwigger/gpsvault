<template>
    <c-form-section title="Two Factor Authentication" description="Add additional security to your account using two factor authentication.">

        <h3 v-if="twoFactorEnabled">
            You have enabled two factor authentication.
        </h3>

        <h3  v-else>
            You have not enabled two factor authentication.
        </h3>

        <div>
            <p>
                When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
            </p>
        </div>

        <div v-if="twoFactorEnabled">
            <div v-if="qrCode">
                <div>
                    <p>
                        Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.
                    </p>
                </div>

                <div class="mt-4" v-html="qrCode">
                </div>
            </div>

            <div v-if="recoveryCodes.length > 0">
                <div>
                    <p>
                        Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                    </p>
                </div>

                <div>
                    <div v-for="code in recoveryCodes" :key="code">
                        {{ code }}
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div v-if="! twoFactorEnabled">
                <c-confirms-password-dialog
                    title="Enable 2FA"
                    description="Enable two factor authentication for your account"
                    button-text="Enable"
                    @confirmed="enableTwoFactorAuthentication">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn :disabled="showing || enabling" @click="trigger">Enable</v-btn>
                    </template>
                </c-confirms-password-dialog>
            </div>

            <div v-else>
                <c-confirms-password-dialog
                    title="Regenerate Recovery Codes"
                    description="Regenerate your recovery codes"
                    button-text="Regenerate"
                    v-if="recoveryCodes.length > 0"
                    @confirmed="regenerateRecoveryCodes">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn :disabled="showing" @click="trigger">Regenerate Recovery Codes</v-btn>
                    </template>
                </c-confirms-password-dialog>

                <c-confirms-password-dialog
                    title="Show Recovery Codes"
                    description="Show your recovery codes"
                    button-text="Show"
                    v-if="recoveryCodes.length === 0"
                    @confirmed="showRecoveryCodes">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn :disabled="showing" @click="trigger">Show Recovery Codes</v-btn>
                    </template>
                </c-confirms-password-dialog>

                <c-confirms-password-dialog
                    title="Disable 2FA"
                    description="Disable Two Factor Authentication"
                    button-text="Disable"
                    v-if="twoFactorEnabled"
                    @confirmed="disableTwoFactorAuthentication">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn :disabled="showing" @click="trigger">Disable</v-btn>
                    </template>
                </c-confirms-password-dialog>
            </div>
        </div>
    </c-form-section>
</template>

<script>
    import CFormSection from './CFormSection';
    import CConfirmsPasswordDialog from '../CConfirmsPasswordDialog';

    export default {
        components: {
            CConfirmsPasswordDialog,
            CFormSection,
        },

        data() {
            return {
                enabling: false,
                disabling: false,

                qrCode: null,
                recoveryCodes: [],
            }
        },

        methods: {
            enableTwoFactorAuthentication() {
                this.enabling = true

                this.$inertia.post('/user/two-factor-authentication', {}, {
                    preserveScroll: true,
                    onSuccess: () => Promise.all([
                        this.showQrCode(),
                        this.showRecoveryCodes(),
                    ]),
                    onFinish: () => (this.enabling = false),
                })
            },

            showQrCode() {
                return axios.get('/user/two-factor-qr-code')
                        .then(response => {
                            this.qrCode = response.data.svg
                        })
            },

            showRecoveryCodes() {
                return axios.get('/user/two-factor-recovery-codes')
                        .then(response => {
                            this.recoveryCodes = response.data
                        })
            },

            regenerateRecoveryCodes() {
                axios.post('/user/two-factor-recovery-codes')
                        .then(response => {
                            this.showRecoveryCodes()
                        })
            },

            disableTwoFactorAuthentication() {
                this.disabling = true

                this.$inertia.delete('/user/two-factor-authentication', {
                    preserveScroll: true,
                    onSuccess: () => (this.disabling = false),
                })
            },
        },

        computed: {
            twoFactorEnabled() {
                return ! this.enabling && this.$page.props.user.two_factor_enabled
            }
        }
    }
</script>
