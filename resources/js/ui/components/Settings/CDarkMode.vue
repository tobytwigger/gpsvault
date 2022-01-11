<template>
    <c-form-section description="Use darker colours on the site." title="Dark mode">
        <v-form @submit.prevent="submit">
            <v-switch
                v-model="form.dark_mode"
                flat
                :label="darkMode ? 'Dark mode on' : 'Dark mode off'"
            ></v-switch>

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
    name: 'CDarkMode',
    components: {
        CFormSection,
    },

    props: {
        initialValue: {
            required: true,
            type: Boolean
        }
    },

    data() {
        return {
            form: this.$inertia.form({
                dark_mode: this.initialValue
            })
        }
    },

    computed: {
        darkMode() {
            return this.form.dark_mode
        }
    },

    watch: {
        darkMode(val) {
            this.$vuetify.theme.dark = val;
        }
    },

    methods: {
        submit() {
            this.form.post(ziggyRoute('settings.store'), {
                errorBag: 'updateDarkMode',
                preserveScroll: true
            });
        },
    },
}
</script>
