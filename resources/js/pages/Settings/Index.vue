<template>
    <c-app-wrapper title="Settings">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>

            <v-tab :href="'#tab-' + groupId" v-for="(group, groupId) in settings" :key="groupId" v-if="group.shouldShow">
                {{ group.name }}
                <v-icon>{{ group.icon }}</v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item v-for="(group, groupId) in settings" :key="groupId" :value="'tab-' + groupId">
                <v-row v-for="(setting, settingKey) in group.components" :key="settingKey" v-if="group.shouldShow && Object.keys(settingValues).length > 0">
                    <v-col>
                        <component :is="setting.component"
                                   :ref="'component-' + settingKey"
                                   v-bind="setting.bind ?? {}"
                                   :model-value="settingValues[settingKey]"
                                   :is-basic-setting="setting.isBasicSetting"
                                   @update:modelValue="$e => updateSetting(settingKey, $e)"
                                   :errors="$page.props.errors.hasOwnProperty(settingKey) ? [$page.props.errors[settingKey]] : []">

                        </component>
                    </v-col>
                </v-row>
            </v-tab-item>
        </v-tabs-items>

        <template #headerActions>
            <v-btn :disabled="!isDirty" :color="isDirty ? 'primary' : 'secondary'" :loading="isSaving" @click="saveSettings">
                Save
            </v-btn>
        </template>

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
import {cloneDeep, isEqual} from 'lodash';
export default {
    name: "Index",
    components: {
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
            tab: null,
            settingValues: {},
            savedSettingValues: {},
            isSaving: false,
            settings: {
                'account': {
                    name: 'Your Account',
                    icon: 'mdi-account',
                    shouldShow: true,
                    components: {
                        'update_profile_information': {
                            component: CUpdateProfileInformationForm,
                            isBasicSetting: false
                        },
                        'update_password': {component: CUpdatePasswordForm, isBasicSetting: false},
                        'delete_user': {component: CDeleteUserForm, isBasicSetting: false}
                    }
                },
                'general': {
                    name: 'General',
                    icon: 'mdi-information',
                    shouldShow: true,
                    components: {
                        'unit_system': {component: CUnitSetting},
                        'dark_mode': {component: CDarkMode},
                        'stats_order_preference': {component: CStatsOrderSetting},
                    }
                },
                'security': {
                    name: 'Security',
                    icon: 'mdi-security',
                    shouldShow: true,
                    components: {
                        '2fa': {
                            component: CTwoFactorAuthenticationForm,
                            isBasicSetting: false,
                        },
                        'logout_other_browser_sessions': {
                            component: CLogoutOtherBrowserSessionsForm,
                            bind: {sessions: this.sessions},
                            isBasicSetting: false,
                        },
                    }
                },
                'global': {
                    name: 'Global',
                    icon: 'mdi-earth',
                    shouldShow: this.$page.props.permissions.indexOf('manage-global-settings') > -1,
                    components: {
                        'strava_client_id': {
                            component: CSystemStravaClient, bind: {clients: this.stravaClients},
                        }
                    },
                },
            }
        }
    },
    mounted() {
        let settingVals = {};
        // Assign an initial value to setting components
        for(let groupId of Object.keys(this.settings)) {
            if(this.settings[groupId].shouldShow) {
                for(let componentId of Object.keys(this.settings[groupId].components)) {
                    if(!this.settings[groupId].components[componentId].hasOwnProperty('isBasicSetting') || this.settings[groupId].components[componentId].isBasicSetting === true) {
                        settingVals[componentId] = this.$settings.getValue(componentId);
                    }
                }
            }
        }
        this.savedSettingValues = cloneDeep(settingVals);
        this.settingValues = cloneDeep(settingVals);
    },
    methods: {
        saveSettings(group) {
            let params = {};
            Object.keys(this.settingValues)
                .filter(settingKey => !isEqual(this.settingValues[settingKey], this.savedSettingValues[settingKey]))
                .forEach(key => params[key] = this.settingValues[key]);

            this.$inertia.post(route('settings.store'), params, {
                onBefore: () => this.isSaving = true,
                onFinish: () => this.isSaving = false,
                onSuccess: () => this.savedSettingValues = cloneDeep(this.settingValues)
            });
        },
        updateSetting(key, value) {
            // Update a setting value in the setting component
            for(let groupId of Object.keys(this.settings)) {
                if(this.settings[groupId].shouldShow) {
                    for(let componentId of Object.keys(this.settings[groupId].components)) {
                        if(componentId === key) {
                            this.settingValues[componentId] = value;
                        }
                    }
                }
            }
        }
    },
    computed: {
        isDirty() {
            return Object.keys(this.settingValues)
                .filter(settingKey => !isEqual(this.settingValues[settingKey], this.savedSettingValues[settingKey]))
                .length > 0;
        },
        // currentTabSchema() {
        //     if(this.tab && Object.keys(this.settings).filter(id => 'tab-' + id === this.tab).length > 0) {
        //         return this.settings[Object.keys(this.settings).filter(id => 'tab-' + id === this.tab)[0]];
        //     }
        //     return this.settings[Object.keys(this.settings)[0]];
        // }
    },
    watch: {
        isDirty(isDirty) {
            window.onbeforeunload = isDirty ? function() {
                return true;
            } : null;
        }
    }
}
</script>

<style scoped>

</style>
