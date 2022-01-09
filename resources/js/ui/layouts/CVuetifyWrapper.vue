<template>
    <div>
        <Head :title="title" />

        <v-app>
            <slot></slot>
        </v-app>

    </div>
</template>

<script>
export default {
    name: "CVuetifyWrapper",
    props: {
        title: String
    }
}
</script>

<style scoped>

</style>
