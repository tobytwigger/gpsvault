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
        apiRoute: {
            required: true,
            type: String
        },
    },
    data() {
        return {
            items: [],
            paginator: null,
            spinner: 'spiral',
        }
    },
    methods: {
        loadNextPage($state) {
            if(this.paginator && (this.paginator?.current_page === this.paginator?.last_page)) {
                $state.complete();
            } else {
                let data = {};
                data[this.pageAttributeName] = (this.paginator?.current_page ?? 0) + 1;
                data[this.perPageAttributeName] = 10;
                axios.get(this.apiRoute, {params: data})
                    .then(response => {
                        this.paginator = response.data;
                        this.items = this.items.concat(this.paginator.data);
                        if(this.paginator.current_page >= this.paginator.last_page) {
                            $state.complete();
                        } else {
                            $state.loaded();
                        }
                    })
                    .catch(() => $state.error());
            }
        },
    }
}
</script>

<style scoped>

</style>
