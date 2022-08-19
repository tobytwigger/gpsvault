<template>
    <v-card
        class="mx-auto"
        max-width="400"
    >
        <v-card-title>{{ title }}</v-card-title>

        <v-card-subtitle class="pb-0">
            {{ subtitle }}
        </v-card-subtitle>

        <v-card-text class="text--primary">
            <div>
                <slot></slot>
            </div>
        </v-card-text>

        <v-card-actions>
            <slot name="actions">

            </slot>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: "CycleStoreDemoCard",
    props: {
        title: null,
        subtitle: null,
    }
}
</script>

<style scoped>

</style>
