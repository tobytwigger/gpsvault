<template>

    <v-navigation-drawer app clipped v-model="shouldShow" :permanent="!$vuetify.breakpoint.mobile">
        <v-list>
            <v-list-item style="justify-content: center;">
                <v-list-item-avatar color="primary" >
                    {{ $page.props.user.name.split(' ').map(i => i.charAt(0).toUpperCase()).join('') }}
                </v-list-item-avatar>
            </v-list-item>

            <v-list-item style="text-align: center;" link>
                <v-list-item-content>
                    <v-list-item-title class="text-h6">
                        {{ $page.props.user.name }}
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list
            nav
            subheader
            dense
        >
            <v-subheader>Activities</v-subheader>

            <v-list-item :input-value="['dashboard'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('dashboard'))">
                <v-list-item-icon>
                    <v-icon>mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Dashboard</v-list-item-title>
            </v-list-item>
            <v-list-item :input-value="['activity.index', 'activity.show'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('activity.index'))"
                         data-hint="See all the rides you've completed in the past." :data-step="993">
                <v-list-item-icon>
                    <v-icon>mdi-bike</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Activities</v-list-item-title>
            </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list
            nav
            subheader
            dense
        >
            <v-subheader>Routes</v-subheader>

            <v-list-item :input-value="['route.index', 'route.show'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('route.index'))"
                         data-hint="See any rides you've previously planned." :data-step="994">
                <v-list-item-icon>
                    <v-icon>mdi-routes</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Routes</v-list-item-title>
            </v-list-item>
            <v-list-item :input-value="['tour.index', 'tour.show'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('tour.index'))"
                         data-hint="Manage your multi-day rides." :data-step="995">
                <v-list-item-icon>
                    <v-icon>mdi-campfire</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Tours</v-list-item-title>
            </v-list-item>
            <v-list-item :input-value="['place.index', 'place.show'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('place.index'))"
                         data-hint="Browse our database of local stops, cafes and accommodation." :data-step="996">
                <v-list-item-icon>
                    <v-icon>mdi-map-marker</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Places</v-list-item-title>
            </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list
            nav
            subheader
            dense
        >
            <v-subheader>Integrations</v-subheader>

            <v-list-item :input-value="['integration.strava'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('integration.strava'))"
                         data-hint="Manage the integration with Strava." :data-step="997">
                <v-list-item-icon>
                    <v-icon v-if="$page.props.integrations.strava.connected">mdi-power-plug</v-icon>
                    <v-icon v-else="$page.props.integrations.strava.connected">mdi-power-plug-off</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Strava</v-list-item-title>
            </v-list-item>


<!--            <v-list-group-->
<!--                :value="['integration.strava', 'strava.client.index'].indexOf(route().current()) > -1"-->
<!--                no-action-->
<!--                prepend-icon="mdi-connection"-->
<!--            >-->
<!--                <template v-slot:activator>-->
<!--                    <v-list-item-content>-->
<!--                        <v-list-item-title>Integrations</v-list-item-title>-->
<!--                    </v-list-item-content>-->
<!--                </template>-->

<!--                <v-list-item :input-value="['integration.strava', 'strava.client.index'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('integration.strava'))"-->
<!--                             data-hint="Manage the integration with Strava." :data-step="997">-->
<!--                    <v-list-item-title>Strava</v-list-item-title>-->
<!--                    <v-list-item-icon>-->
<!--                        <v-icon>mdi-arrow-right</v-icon>-->
<!--                    </v-list-item-icon>-->
<!--                </v-list-item>-->
<!--            </v-list-group>-->

        </v-list>

        <v-divider></v-divider>

        <v-list
            nav
            subheader
            dense
        >
            <v-subheader>Account</v-subheader>

            <v-list-item :input-value="['backup.index'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('backup.index'))"
                         data-hint="Create and download backups of all your data." :data-step="998">
                <v-list-item-icon>
                    <v-icon>mdi-safe</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Backups</v-list-item-title>
            </v-list-item>
            <v-list-item :input-value="['documentation'].indexOf(route().current()) > -1" link target="_blank" :href="route('documentation')"
                         data-hint="Learn how to use this site." :data-step="999">
                <v-list-item-icon>
                    <v-icon>mdi-book-open-blank-variant</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Documentation</v-list-item-title>
            </v-list-item>
            <v-list-item :input-value="['settings.index'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('settings.index'))"
                         data-hint="Manage your preferences." :data-step="1000">
                <v-list-item-icon>
                    <v-icon>mdi-cog</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Settings</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="$inertia.post(route('logout'))">
                <v-list-item-icon>
                    <v-icon>mdi-logout</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Log Out</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>

</template>

<script>
export default {
    name: "CSidebar",
    props: {
        value: {
            required: true,
            type: Boolean
        }
    },
    computed: {
        shouldShow: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            }
        }
    }
}
</script>

<style scoped>

</style>
