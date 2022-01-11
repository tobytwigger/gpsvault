<template>
    <c-form-section description="Which Strava client should be used by default by new users?." title="Strava Client">
        <v-form @submit.prevent="submit">
            <v-select
                id="strava_client_id"
                v-model="form.strava_client_id"
                :error="form.errors.hasOwnProperty('strava_client_id')"
                :error-messages="form.errors.hasOwnProperty('strava_client_id') ? [form.errors.strava_client_id] : []"
                :items="clients"
                hint="Which of your clients should be used for all users if they can't add their own clients?"
                item-text="name"
                item-value="id"
                label="Strava client"
                name="strava_client_id"
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
    name: "CSystemStravaClient",
    components: {CFormSection},
    props: {
        clients: {
            required: true,
            type: Array
        },
        initialValue: {
            required: false,
            type: Number,
            default: null
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                system_client_id: this.initialValue
            })
        }
    },
    methods: {
        submit() {
            this.form.post(ziggyRoute('settings.store'), {
                errorBag: 'updateDarkMode',
                preserveScroll: true
            })
        }
    }
}
</script>

<style scoped>

</style>
