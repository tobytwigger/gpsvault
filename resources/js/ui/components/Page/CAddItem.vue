<template>
    <div>
        <v-menu offset-y :close-on-content-click="false">
            <template v-slot:activator="{ on, attrs }">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on: onTooltip, attrs: attrsTooltip }">
                        <v-btn
                            fab
                            small
                            v-bind="{...attrs, ...attrsTooltip}"
                            v-on="{...on, ...onTooltip}"
                        >
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <span>Create</span>
                </v-tooltip>
            </template>
            <v-list>
                <c-activity-form title="Add new activity" button-text="Create">
                    <template v-slot:activator="{trigger, showing}">
                        <v-list-item @click="trigger">
                            <v-list-item-icon><v-icon>mdi-bike</v-icon></v-list-item-icon>
                            <v-list-item-title>Ride</v-list-item-title>
                        </v-list-item>
                    </template>
                </c-activity-form>

                <c-route-form title="Add new route" button-text="Create">
                    <template v-slot:activator="{trigger, showing}">
                        <v-list-item @click="trigger">
                            <v-list-item-icon><v-icon>mdi-routes</v-icon></v-list-item-icon>
                            <v-list-item-title>Route</v-list-item-title>
                        </v-list-item>
                    </template>
                </c-route-form>

            </v-list>
        </v-menu>
    </div>
</template>

<script>
import CRouteForm from '../Route/CRouteForm';
import CActivityForm from '../Activity/CActivityForm';
export default {
    name: "CAddItem",
    components: {CActivityForm, CRouteForm}
}
</script>

<style scoped>

</style>
