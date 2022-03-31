<template>
    <c-app-wrapper title="Settings">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>

            <v-tab href="#tab-account">
                Your Account
                <v-icon>mdi-account</v-icon>
            </v-tab>

            <v-tab href="#tab-general">
                General
                <v-icon>mdi-information</v-icon>
            </v-tab>

            <v-tab href="#tab-security">
                Security
                <v-icon>mdi-security</v-icon>
            </v-tab>

            <v-tab href="#tab-earth" v-if="$page.props.permissions.indexOf('manage-global-settings') > -1">
                Global
                <v-icon>mdi-earth</v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-account">
                <v-row>
                    <v-col>
                        <c-update-profile-information-form></c-update-profile-information-form>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <c-delete-user-form></c-delete-user-form>
                    </v-col>
                </v-row>
            </v-tab-item>
            <v-tab-item value="tab-general">
                <v-row>
                    <v-col>
                        <c-unit-setting>

                        </c-unit-setting>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <c-dark-mode>

                        </c-dark-mode>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <c-stats-order-setting></c-stats-order-setting>
                    </v-col>
                </v-row>
            </v-tab-item>
            <v-tab-item value="tab-security">
                <v-row>
                    <v-col>
                        <c-update-password-form></c-update-password-form>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <c-logout-other-browser-sessions-form :sessions="sessions"></c-logout-other-browser-sessions-form>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <c-two-factor-authentication-form></c-two-factor-authentication-form>
                    </v-col>
                </v-row>
            </v-tab-item>
            <v-tab-item value="tab-earth" v-if="$page.props.permissions.indexOf('manage-global-settings') > -1">
                <v-row>
                    <v-col>
                        <c-system-strava-client
                            v-if="$page.props.permissions.indexOf('manage-global-settings') > -1"
                            :clients="stravaClients">

                        </c-system-strava-client>

                        <c-bruit-api-key
                            v-if="$page.props.permissions.indexOf('manage-bruit-key') > -1">

                        </c-bruit-api-key>
                    </v-col>
                </v-row>
            </v-tab-item>
        </v-tabs-items>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CUpdateProfileInformationForm from 'ui/components/Settings/CUpdateProfileInformationForm';
import CDeleteUserForm from 'ui/components/Settings/CDeleteUserForm';
import CUpdatePasswordForm from 'ui/components/Settings/CUpdatePasswordForm';
import CLogoutOtherBrowserSessionsForm from 'ui/components/Settings/CLogoutOtherBrowserSessionsForm';
import CTwoFactorAuthenticationForm from 'ui/components/Settings/CTwoFactorAuthenticationForm';
import CUnitSetting from 'ui/components/Settings/CUnitSetting';
import CDarkMode from 'ui/components/Settings/CDarkMode';
import CSystemStravaClient from 'ui/components/Settings/CSystemStravaClient';
import CStatsOrderSetting from 'ui/components/Settings/CStatsOrderSetting';
import CBruitApiKey from '../../ui/components/Settings/CBruitApiKey';
export default {
    name: "Index",
    components: {
        CBruitApiKey,
        CStatsOrderSetting,
        CSystemStravaClient,
        CDarkMode,
        CUnitSetting,
        CTwoFactorAuthenticationForm,
        CLogoutOtherBrowserSessionsForm,
        CUpdatePasswordForm, CDeleteUserForm, CUpdateProfileInformationForm, CAppWrapper
    },
    props: {
        sessions: {
            required: false,
            type: Array,
            default: () => []
        },
        stravaClients: {
            required: false,
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            tab: null
        }
    }
}
</script>

<style scoped>

</style>
