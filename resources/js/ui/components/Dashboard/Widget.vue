<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
        style="text-align: center;"
    >

        <v-list-item three-line>
            <v-list-item-content>
                <div class="mb-4">
                    {{widgetName}}
                </div>

                <slot>
                    <v-list-item-title class="text-h5 mb-1">
                        {{ data }}
                    </v-list-item-title>
                    <v-list-item-subtitle>{{ description }}</v-list-item-subtitle>
                </slot>
            </v-list-item-content>
        </v-list-item>
    </v-card>
</template>

<script>
export default {
    name: "Widget",
    props: {
        widgetName: {
            required: true,
            type: String
        },

        data: {
            required: false,
            type: String,
            default: null
        },
        description: {
            required: false,
            type: String,
            default: null
        }
    }
}
</script>

<style scoped>

</style>
