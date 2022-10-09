<template>
    <v-footer
        color="grey lighten-2"
        padless
    >
        <v-row
            justify="center"
            no-gutters
        >
            <v-col
                class="grey lighten-2 py-4 text-center"
                cols="12"
            >
                 <strong>&copy;Toby Twigger</strong> - {{ new Date().getFullYear() }}
            </v-col>
        </v-row>
    </v-footer>
</template>

<script>
export default {
    name: "CFooter"
}
</script>

<style scoped>

</style>
