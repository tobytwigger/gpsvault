<template>
    <c-form-section description="Change the units we use to show you data." title="Units">
        <v-select
            id="unit_system"
            v-model="value"
            :error="errors.length > 0"
            :error-messages="errors"
            :items="units"
            hint="Which units should we show data in by default?"
            item-text="text"
            item-value="value"
            label="Units"
            name="unit_system"
            prepend-icon="mdi-numeric"
        ></v-select>
    </c-form-section>
</template>

<script>
import CFormSection from './CFormSection';
import settingCard from '../../mixins/settingCard';

export default {
    name: 'CUnitSetting',
    components: {
        CFormSection,
    },
    mixins: [settingCard],

    data() {
        return {
            units: [
                {text: 'Metric', value: 'metric'},
                {text: 'Imperial', value: 'imperial'},
            ]
        }
    },
}
</script>
