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
        fetchItems: {
            required: true,
            type: Function // Accepts a page parameter
        }
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
            this.fetchItems(this.nextPageToLoad)
                .then(response => {
                    console.log(response.data);
                    this.items = this.items.concat(response.data.data);
                    this.nextPageToLoad += 1;
                    $state.loaded();
                    if(this.nextPageToLoad > response.data.last_page) {
                        $state.completed();
                    }
                })
                .catch(() => {
                    $state.error();
                })
        },
    }
}
</script>

<style scoped>

</style>
