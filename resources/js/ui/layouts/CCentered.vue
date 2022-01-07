<template>
    <v-row
        align="center"
        class="fill-height"
        justify="center">
        <v-col
            cols="12"
            md="4"
            sm="8">
            <slot name="alerts"></slot>
            <slot></slot>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: "CCentered"
}
</script>

<style scoped>

</style>
