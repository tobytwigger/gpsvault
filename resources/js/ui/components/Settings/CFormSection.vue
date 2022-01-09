<template>
    <div>
        <v-row>
            <v-col>
                {{ title }} -
                {{ description }}
                <slot name="body"></slot>
            </v-col>
            <v-col>
                <slot></slot>
                <slot name="actions"></slot>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: "CFormSection",
    props: {
        title: {
            required: true,
            type: String
        },
        description: {
            required: true,
            type: String
        },
    }
}
</script>

<style scoped>

</style>
