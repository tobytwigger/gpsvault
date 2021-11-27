<template>
    <div class="mt-4">
        <p>We can't automatically import the gps files when synchronising from Strava. Fortunately, you can quickly attach all these files at once.</p>

        <p class="text-sm mt-4">Follow the documentation to get your zip file from Strava, then upload it below.</p>

        <div v-if="errors !== null">
            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
            </ul>
        </div>
        <div class="mt-4">
            <jet-label for="file" value="Zip file" />
            <jet-input id="file" type="file" class="mt-1 block w-full" @change="$emit('input', {file: $event.target.files})" required />
        </div>
    </div>
</template>

<script>
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'

export default {
    name: "StravaFixSetup",
    components: {
        JetInput,
        JetLabel,
    },
    props: {
        value: {
            required: false
        },
        errors: {
            required: false,
            type: Object,
            default: null
        }
    }
}
</script>

<style scoped>

</style>
