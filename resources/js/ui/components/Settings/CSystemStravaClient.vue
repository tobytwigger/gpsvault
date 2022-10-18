<template>
    <c-form-section description="Which Strava client should be used by default by new users?." title="Strava Client">
        <v-select
            id="strava_client_id"
            v-model="value"
            :error="errors.length > 0"
            :error-messages="errors"
            :items="clients"
            hint="Which of your clients should be used for all users if they can't add their own clients?"
            item-text="name"
            item-value="id"
            label="Strava client"
            name="strava_client_id"
            prepend-icon="mdi-numeric"
        ></v-select>
    </c-form-section>
</template>

<script>
import CFormSection from './CFormSection';
import settingCard from '../../mixins/settingCard';
export default {
    name: "CSystemStravaClient",
    components: {CFormSection},
    mixins: [settingCard],
    props: {
        clients: {
            required: true,
            type: Array
        },
    }
}
</script>

<style scoped>

</style>
