<template>
    <sidebar-menu :menu="routes" :relative="true" theme="white-theme" :link-component-name="'v-sidebar-link'">

    </sidebar-menu>
</template>

<script>
export default {
    name: "VSidebar",
    props: {
        routes: {
            required: true,
            type: Object
        },
        show: {
            required: true,
            type: Boolean
        }
    },
}
</script>

<style scoped>

</style>
