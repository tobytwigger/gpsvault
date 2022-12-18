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
        <infinite-loading :key="uniqueInifiniteLoadingId" @infinite="loadNextPage" :spinner="spinner"></infinite-loading>

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
    watch: {
        paginator: {
            deep: true,
            handler: function() {
                this.items = this.shouldMergeItems ? [...this.items, ...this.paginator.data] : this.paginator.data;
                if(this.shouldMergeItems === false) {
                    // Reset the infinite loading state
                    this.uniqueInifiniteLoadingId = (Math.random() + 1).toString(36).substring(7);
                }
            }
        }
    },
    data() {
        return {
            uniqueInifiniteLoadingId: (Math.random() + 1).toString(36).substring(7),
            items: this.paginator.data,
            spinner: 'spiral',
            shouldMergeItems: false
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
                this.$inertia.get(this.paginator.path, data, {
                    preserveState: true,
                    preserveScroll: true,
                    onBefore: () => {
                        this.shouldMergeItems = true;
                    },
                    onSuccess: () => {
                        if(this.paginator.current_page >= this.paginator.last_page) {
                            $state.complete();
                        } else {
                            $state.loaded();
                        }
                        window.history.replaceState({}, this.$page.title, this.paginator?.path ?? this.$page.url);
                    },
                    onError: () => {
                        $state.error();
                    },
                    onFinish: () => {
                        this.shouldMergeItems = false;
                    }
                });
            }
        },
    }
}
</script>

<style scoped>

</style>
