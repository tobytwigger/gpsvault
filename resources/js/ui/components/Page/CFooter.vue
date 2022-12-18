<template>
    <v-footer
        color="grey lighten-2"
        padless
    >
        <v-row
            justify="center"
        >
            <v-col
                class="grey lighten-2 text-center"
                cols="12"
            >
                 <small>
                     <strong>&copy;Toby Twigger</strong> - {{ new Date().getFullYear() }}. All rights reserved.
                 </small>
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
