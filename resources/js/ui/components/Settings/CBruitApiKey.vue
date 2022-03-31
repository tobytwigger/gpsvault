<template>
    <c-form-section description="The API key for Bruit, the feedback platform.." title="Units">
        <v-form @submit.prevent="submit">
            <v-input
                type="text"
                id="bruit_api_key"
                v-model="form.bruit_api_key"
                :error="form.errors.hasOwnProperty('bruit_api_key')"
                :error-messages="form.errors.hasOwnProperty('bruit_api_key') ? [form.errors.bruit_api_key] : []"
                :items="units"
                hint="Get this from bruit.io"
                label="Bruit API Key"
                name="bruit_api_key"
            ></v-input>
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
    name: 'CBruitApiKey',
    components: {
        CFormSection,
    },

    data() {
        return {
            form: this.$inertia.form({
                bruit_api_key: this.$setting.bruit_api_key
            }),
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
