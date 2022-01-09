<template>
    <c-form-section description="Change the units we use to show you data." title="Units">
        <v-form @submit.prevent="submit">
            <v-select
                id="unit_system"
                v-model="form.unit_system"
                :error="form.errors.hasOwnProperty('unit_system')"
                :error-messages="form.errors.hasOwnProperty('unit_system') ? [form.errors.unit_system] : []"
                :items="units"
                hint="Which units should we show data in by default?"
                item-text="text"
                item-value="value"
                label="Units"
                name="unit_system"
                prepend-icon="mdi-numeric"
            ></v-select>
        </v-form>

        <template #actions>
            <v-btn :disabled="form.processing" :loading="form.processing" @click="submit">
                Save
            </v-btn>
        </template>
    </c-form-section>
</template>

<script>
import CFormSection from './CFormSection';

export default {
    name: 'CUnitSetting',
    components: {
        CFormSection,
    },

    props: {
        initialValue: {
            required: true,
            type: String
        }
    },

    data() {
        return {
            form: this.$inertia.form({
                unit_system: this.initialValue
            }),
            units: [
                {text: 'Metric', value: 'metric'},
                {text: 'Imperial', value: 'imperial'},
            ]
        }
    },

    methods: {
        submit() {
            this.form.post(route('settings.store'), {
                errorBag: 'updateUnitSetting',
                preserveScroll: true
            });
        },
    },
}
</script>
