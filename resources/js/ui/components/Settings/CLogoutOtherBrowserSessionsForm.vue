<template>
    <c-form-section title="Browser Sessions" description="Manage and log out your active sessions on other browsers and devices">

        <h3 v-if="sessions.length === 0">
            No sessions have been found
        </h3>

        <div>
            <p>
                If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.
            </p>
        </div>

        <v-list v-if="sessions.length > 0">
            <v-list-item v-for="(session, i) in sessions" :key="i">
                <v-list-item-icon>
                    <v-icon v-if="session.agent.is_desktop">mdi-monitor</v-icon>
                    <v-icon v-else>mdi-cellphonne</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{ session.agent.platform }} - {{ session.agent.browser }}</v-list-item-title>
                    <v-list-item-subtitle>{{ session.ip_address }}</v-list-item-subtitle>
                    <v-list-item-subtitle v-if="session.is_current_device">This device</v-list-item-subtitle>
                    <v-list-item-subtitle v-else>Last active {{ session.last_active }}</v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>
        </v-list>

        <c-confirmation-dialog title="Log out of other sessions" button-text="Log out" :loading="form.processing" @confirm="logoutOtherBrowserSessions">
            <template v-slot:activator="{trigger,showing}">
                <v-btn @click.stop="trigger" :loading="form.processing" :disabled="showing" v-if="sessions.length > 0">
                    <v-icon>mdi-logout</v-icon>
                    Log out other browser sessions
                </v-btn>
            </template>
            Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.

            <v-form @submit.prevent="logoutOtherBrowserSessions">
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
        props: ['sessions'],

        components: {
            CConfirmationDialog,
            CFormSection,
        },

        data() {
            return {
                form: this.$inertia.form({
                    password: '',
                })
            }
        },

        methods: {
            logoutOtherBrowserSessions() {
                this.form.delete(route('other-browser-sessions.destroy'), {
                    preserveScroll: true,
                    onError: () => this.$refs.password.focus(),
                    onFinish: () => this.form.reset(),
                })
            },
        },
    }
</script>
