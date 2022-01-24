<template>

    <div v-if="startedAt || endedAt">
        <div v-if="startedAt && !endedAt">
            Ride starting from <v-chip class="ma-2" color="green" text-color="white"><v-icon>mdi-map-marker</v-icon>{{startedAt}}</v-chip>
        </div>
        <div v-else-if="!startedAt && endedAt">
            Ride ending at <v-chip class="ma-2" color="green" text-color="white"><v-icon>mdi-map-marker</v-icon>{{endedAt}}</v-chip>
        </div>
        <div v-else-if="startedAt === endedAt">
            Loop from <v-chip class="ma-2" color="green" text-color="white"><v-icon>mdi-map-marker</v-icon>{{startedAt}}</v-chip>
        </div>
        <div v-else>
            Ride from <v-chip class="ma-2" color="green" text-color="white"><v-icon>mdi-map-marker</v-icon>{{startedAt}}</v-chip> to <v-chip class="ma-2" color="green" text-color="white"><v-icon>mdi-map-marker</v-icon>{{endedAt}}</v-chip>
        </div>
    </div>
</template>

<script>
export default {
    name: "CActivityLocationSummary",
    props: {
        startedAt: {
            required: false,
            type: String,
            default: null
        },
        endedAt: {
            required: false,
            type: String,
            default: null
        },
    }
}
</script>

<style scoped>

</style>
