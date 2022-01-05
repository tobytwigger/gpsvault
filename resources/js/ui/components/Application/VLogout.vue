<template>
    <form method="POST" @submit.prevent="logout">
        <slot></slot>
    </form>
</template>

<script>
export default {
    name: "VLogout",
    methods: {
        logout() {
            this.$inertia.post(route('logout'));
        },
    }
}
</script>

<style scoped>

</style>
