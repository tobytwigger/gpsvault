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
            dense
        >
            <v-list-item :input-value="['dashboard'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('dashboard'))">
                <v-list-item-icon>
                    <v-icon>mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Dashboard</v-list-item-title>
            </v-list-item>
            <v-list-item :input-value="['activity.index', 'activity.show'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('activity.index'))">
                <v-list-item-icon>
                    <v-icon>mdi-bike</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Activities</v-list-item-title>
            </v-list-item>
            <v-list-item link>
                <v-list-item-icon>
                    <v-icon>mdi-routes</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Routes</v-list-item-title>
            </v-list-item>
            <v-list-item link>
                <v-list-item-icon>
                    <v-icon>mdi-campfire</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Tours</v-list-item-title>
            </v-list-item>

        </v-list>

        <v-divider></v-divider>

        <v-list
            nav
            dense
        >
            <v-list-group
                :value="false"
                no-action
                prepend-icon="mdi-connection"
            >
                <template v-slot:activator>
                    <v-list-item-content>
                        <v-list-item-title>Integrations</v-list-item-title>
                    </v-list-item-content>
                </template>

                <v-list-item :input-value="['strava.client.index'].indexOf(route().current()) > -1" link @click="$inertia.get(route('strava.client.index'))">
                    <v-list-item-title>Strava</v-list-item-title>
                    <v-list-item-icon>
                        <v-icon>mdi-arrow-right</v-icon>
                    </v-list-item-icon>
                </v-list-item>
            </v-list-group>
            <v-list-item :input-value="['backups.index'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('backups.index'))">
                <v-list-item-icon>
                    <v-icon>mdi-safe</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Backups</v-list-item-title>
            </v-list-item>
            <v-list-item :input-value="['documentation'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('documentation'))">
                <v-list-item-icon>
                    <v-icon>mdi-book-open-blank-variant</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Documentation</v-list-item-title>
            </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list
            nav
            dense
        >
            <v-list-item :input-value="['settings.index'].indexOf(route().current()) > -1" link @click="$inertia.visit(route('settings.index'))">
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
