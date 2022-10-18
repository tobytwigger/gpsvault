<template>
    <c-form-section description="Use darker colours on the site." title="Dark mode">
        <v-switch
            v-model="value"
            flat
            :error="errors.length > 0"
            :error-messages="errors"
            :label="value ? 'Dark mode on' : 'Dark mode off'"
        ></v-switch>

    </c-form-section>
</template>

<script>
import CFormSection from './CFormSection';
import settingCard from '../../mixins/settingCard';

export default {
    name: 'CDarkMode',
    components: {
        CFormSection,
    },
    mixins: [settingCard],

    watch: {
        value(val) {
            console.log(val);
            this.$vuetify.theme.dark = val;
        }
    },
}
</script>
