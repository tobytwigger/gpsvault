<template>
    <div>
        <v-list-item>

            <v-list-item-action>
                <v-progress-circular indeterminate v-if="hasActivities"></v-progress-circular>
                <v-icon v-else>{{icon}}</v-icon>
            </v-list-item-action>

            <v-list-item-content>
                <v-list-item-title>{{ title }}</v-list-item-title>
                <v-list-item-subtitle>{{description}}</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action v-if="hasActivities">

                <h1>{{activitiesCount}}</h1>

                <v-btn icon @click="showDialog = true" :disabled="showDialog === true">
                    <v-icon color="grey lighten-1">mdi-information</v-icon>
                </v-btn>
            </v-list-item-action>
        </v-list-item>

        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    {{ title }}
                </v-card-title>
                <v-card-text>
                    <v-lazy
                        :options="{
                          threshold: .5
                        }"
                        min-height="200"
                        transition="fade-transition"
                    >

                        <v-list>
                            <v-list-item
                                v-for="activity in activities"
                                :key="activity.id"
                                @click="$inertia.get(route('activity.show', activity.id))">
                                <v-list-item-content>
                                    <v-list-item-title>{{ activity.name }}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-lazy>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                    >
                        Close
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "CSyncInformationItem",
    props: {
        activities: {
            required: true,
            type: Array
        },
        title: {
            required: true,
            type: String
        },
        description: {
            required: true,
            type: String
        },
        icon: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            showDialog: false
        }
    },
    computed: {
        hasActivities() {
            return this.activitiesCount > 0;
        },
        activitiesCount() {
            return this.activities.length;
        }
    }
}
</script>

<style scoped>

</style>
