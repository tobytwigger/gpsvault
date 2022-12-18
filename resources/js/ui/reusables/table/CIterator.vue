<template>

    <div>
        <c-infinite-scroll-iterator :prepend="prepend" :paginator="paginator" v-if="infiniteScroll" :layout="layout" :item-key="itemKey" :list-headers="listHeaders">
            <template v-slot:prepend>
                <slot name="prepend"></slot>
            </template>
            <template v-slot:default="attrs">
                <slot name="default" v-bind="attrs"></slot>
            </template>
            <template v-slot:list="attrs">
                <slot name="list" v-bind="attrs"></slot>
            </template>
        </c-infinite-scroll-iterator>

        <c-pagination-iterator v-else :layout="layout" :paginator="paginator" :item-key="itemKey" :list-headers="listHeaders">
            <template v-slot:default="attrs">
                <slot name="default" v-bind="attrs"></slot>
            </template>
            <template v-slot:list="attrs">
                <slot name="list" v-bind="attrs"></slot>
            </template>
        </c-pagination-iterator>

        <div v-else>
            No paginator or API route found. Please contact support.
        </div>

    </div>

</template>

<script>
import CInfiniteScrollIterator from './CInfiniteScrollIterator';
import CPaginationIterator from './CPaginationIterator';
export default {
    name: "CIterator",
    components: {CPaginationIterator, CInfiniteScrollIterator},
    props: {
        paginator: {
            required: true,
            type: Object
        },
        prepend: {
            required: false,
            type: Boolean,
            default: false
        },
        layout: {
            required: false,
            type: String,
            default: 'cards',
            validator: (val) => ['cards', 'list'].includes(val)
        },
        infiniteScroll: {
            required: false,
            type: Boolean,
            default: false
        },
        itemKey: {
            required: true,
            type: String
        },
        listHeaders: {
            required: false,
            type: Array,
            default: () => []
        },

    },
}
</script>

<style scoped>

</style>
