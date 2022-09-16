<template>
    <div>
        <v-list>
            <v-list-item v-for="activity in activities" :key="activity.id">
                <v-list-item-content>
                    <v-list-item-title>{{ activity.name }}</v-list-item-title>
                    <v-list-item-subtitle><a :href="route('activity.show', activity.id)">{{ route('activity.show', activity.id) }}</a></v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action>
                    <c-link-strava-activity-form :activity="activity">
                        <template v-slot:activator="{trigger, showing}">
                            <v-tooltip bottom>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        icon
                                        link
                                        @click="trigger"
                                        :disabled="showing"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                </template>
                                Edit
                            </v-tooltip>
                        </template>
                    </c-link-strava-activity-form>
                </v-list-item-action>
            </v-list-item>
        </v-list>
    </div>
</template>

<script>
import CLinkStravaActivityForm from './CLinkStravaActivityForm';
export default {
    name: "CLinkActivity",
    components: {CLinkStravaActivityForm},
    props: {
        activities: {
            required: true,
            type: Array
        }
    }
}
</script>

<style scoped>

</style>
