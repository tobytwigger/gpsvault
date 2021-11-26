<template>
    <div class="mt-4">
        <p>We can't automatically import the gps files when synchronising from Strava. Fortunately, you can quickly attach all these files at once.</p>

        <p class="text-sm mt-4">Follow the documentation to get your zip file from Strava, then upload it below.</p>

        <div class="mt-4">
            <jet-label for="file" value="Zip file" />
            <jet-input id="file" type="file" class="mt-1 block w-full" @change="$emit('input', {file: $event.target.files})" required />
        </div>

    </div>
</template>

<script>
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'

export default {
    name: "StravaFixSetup",
    components: {
        JetInput,
        JetLabel,
    },
    props: {
        value: {
            required: false
        }
    }
}
</script>

<style scoped>

</style>
