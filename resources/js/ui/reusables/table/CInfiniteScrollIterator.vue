<template>

    <div>
        <div v-if="layout === 'cards'">
            <c-grid-table :items="items"
                          :item-key="itemKey"
                          :prepend="prepend">

                <template v-slot:default="attrs">
                    <slot name="default" v-bind="attrs"></slot>
                </template>

                <template v-slot:footer>
                    <slot name="footer"></slot>
                </template>

            </c-grid-table>

        </div>
        <infinite-loading @infinite="loadNextPage" :spinner="spinner"></infinite-loading>

    </div>

</template>

<script>
import InfiniteLoading from 'vue-infinite-loading';

export default {
    name: "CInfiniteScrollIterator",
    components: {InfiniteLoading},
    props: {
        layout: {
            required: false,
            type: String,
            default: 'cards',
            validator: (val) => ['cards', 'list'].includes(val)
        },
        itemKey: {
            required: true,
            type: String
        },
        pageAttributeName: {
            required: false,
            type: String,
            default: 'page'
        },
        perPageAttributeName: {
            required: false,
            type: String,
            default: 'perPage'
        },
        prepend: {
            required: false,
            type: Boolean,
            default: false
        },
        listHeaders: {
            required: false,
            type: Array,
            default: () => []
        },
        paginator: {
            required: true,
            type: Object
        },
    },
    data() {
        return {
            items: [],
            nextPageToLoad: 1,
            spinner: 'spiral'
        }
    },
    methods: {
        loadNextPage($state) {
            let data = {};
            data[this.pageAttributeName] = this.nextPageToLoad;
            data[this.perPageAttributeName] = 10;
            this.$inertia.get(this.paginator.path, data, {
                replace: true,
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    this.items = this.items.concat(this.paginator.data);
                    this.nextPageToLoad += 1
                    $state.loaded();
                    if(this.nextPageToLoad > this.paginator.last_page) {
                        $state.completed();
                    }
                },
                onError: () => {
                    $state.error();
                },
            });
        },
    }
}
</script>

<style scoped>

</style>
