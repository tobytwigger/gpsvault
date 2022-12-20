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

                <template v-slot:prepend>
                    <slot name="prepend"></slot>
                </template>

            </c-grid-table>

        </div>
        <infinite-loading :key="uniqueInfiniteLoadingId" @infinite="loadNextPage" :spinner="spinner"></infinite-loading>

    </div>

</template>

<script>
import InfiniteLoading from 'vue-infinite-loading';

export default {
    name: "CApiScrollIterator",
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
    watch: {
        paginator: {
            deep: true,
            handler: function() {
                if(this.paginator) {
                    this.items = this.shouldMergeItems ? [...this.items, ...this.paginator.data] : this.paginator.data;
                    if(this.shouldMergeItems === false) {
                        // Reset the infinite loading state
                        this.uniqueInfiniteLoadingId = (Math.random() + 1).toString(36).substring(7);
                    }
                }
            }
        }
    },
    data() {
        return {
            uniqueInfiniteLoadingId: (Math.random() + 1).toString(36).substring(7),
            items: [],
            spinner: 'spiral',
            shouldMergeItems: false,
            paginator: null
        }
    },
    methods: {
        loadNextPage($state) {
            if(this.paginator && this.paginator?.next_page_url === null) {
                $state.complete();
            } else {
                let data = {};
                data[this.pageAttributeName] = (this.paginator?.current_page ?? 0) + 1;
                data[this.perPageAttributeName] = 10;
                this.shouldMergeItems = true;
                axios.get(this.apiRoute, {params: data})
                    .then(response => {
                        this.paginator = response.data;
                        if(this.paginator.current_page >= this.paginator.last_page) {
                            $state.complete();
                        } else {
                            $state.loaded();
                        }
                    })
                    .catch(() => $state.error())
                    .then(() => this.shouldMergeItems = false);
            }
        },
    }
}
</script>

<style scoped>

</style>
