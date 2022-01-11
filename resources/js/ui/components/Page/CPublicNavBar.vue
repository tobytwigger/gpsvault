<template>

    <v-app-bar app clipped-left>
        <v-spacer></v-spacer>

        <v-toolbar-title>Cycle Store - {{title}}</v-toolbar-title>

        <v-spacer></v-spacer>

        <div v-if="$page.props.user">
            <Link :href="ziggyRoute('dashboard')">Dashboard</Link>
        </div>
        <div v-else>
            <Link :href="ziggyRoute('login')">Login</Link>
            <Link :href="ziggyRoute('register')">Register</Link>
        </div>
        <!-- -->
    </v-app-bar>

</template>

<script>

export default {
    name: "CPublicNavBar",
    props: {
        drawer: {
            required: true,
            type: Boolean
        },
        title: {
            required: true,
            type: String
        }
    },
    computed: {
        showingSidebar: {
            get() {
                return this.sidebar;
            },
            set(val) {
                this.$emit('update:sidebar', val)
            }
        }
    }
}
</script>

<style scoped>

</style>
