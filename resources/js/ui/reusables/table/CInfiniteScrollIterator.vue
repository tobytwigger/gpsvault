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
    mounted() {
    },
    data() {
        return {
            items: this.paginator.data,
            spinner: 'spiral',
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
                console.log(this.paginator.path, data);
                this.$inertia.get(this.paginator.path, data, {
                    preserveState: true,
                    preserveScroll: true,
                    onBefore: () => {
                        console.log('as');
                    },
                    onSuccess: () => {
                        console.log('HI');
                        this.items = [...this.items, ...this.paginator.data];
                        if(this.paginator.current_page >= this.paginator.last_page) {
                            $state.complete();
                            console.log('Done');
                        } else {
                            $state.loaded();
                            console.log('loaded');
                        }
                    },
                    onError: (e) => {
                        console.log('s');
                        console.log(e)
                        $state.error();
                    },
                    onFinish: (visit) => {
                        console.log('done');
                        console.log(visit);
                        window.history.replaceState({}, this.$page.title, this.paginator?.path ?? this.$page.url);
                    }
                });
            }
        },
    }
}
</script>

<style scoped>

</style>
