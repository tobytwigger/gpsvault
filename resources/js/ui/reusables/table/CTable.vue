<template>
    <div>
        <span v-if="loading && items.length === 0">
            Looking for items...
        </span>
        <span v-else-if="items.length === 0">
            No items found
        </span>
        <div v-else>
            <v-simple-table>
                <thead>
                <tr>
                    <th v-for="(header, i) in headers" :key="i" class="text-left">
                        {{ header }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) in items"
                    :key="item[itemKey]">
                    <slot name="list" v-bind:item="item" v-bind:isFirst="index === 0"></slot>
                </tr>
                </tbody>
            </v-simple-table>
        </div>
    </div>
</template>

<script>
export default {
    name: "CTable",
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
        headers: {
            required: false,
            type: Array,
            default: () => []
        }
    }
}
</script>

<style scoped>

</style>
