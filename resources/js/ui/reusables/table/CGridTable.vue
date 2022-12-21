<template>

    <div>
        <div>
        <span v-if="loading && items.length === 0">
            Looking for items...
        </span>
            <span v-else-if="items.length === 0">
            No items found
        </span>
            <div v-else>
                <v-row>
                    <v-col
                        v-if="prepend"
                        cols="12"
                        xl="3"
                        md="4"
                        sm="6"
                    >
                        <slot name="prepend"></slot>
                    </v-col>
                    <v-col
                        v-for="(item, index) in items"
                        :key="item[itemKey]"
                        cols="12"
                        xl="3"
                        md="4"
                        sm="6"
                    >
                        <slot name="default" v-bind:item="item" v-bind:isFirst="index === 0" v-bind:index="index"></slot>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <slot name="footer">

                        </slot>
                    </v-col>
                </v-row>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: "CGridTable",
    props: {
        loading: {
            required: false,
            type: Boolean,
            default: false
        },
        items: {
            required: false,
            type: Array,
            default: () => []
        },
        itemKey: {
            required: true,
            type: String
        },
        prepend: {
            required: false,
            type: Boolean,
            default: false
        }
    }
}
</script>

<style scoped>

</style>
