<template>

    <div>
        <div v-if="layout === 'cards'">
            <c-grid-table :items="items"
                          :item-key="itemKey"
                          :loading="loading"
                          :prepend="prepend">

                <template v-slot:default="attrs">
                    <slot name="default" v-bind="attrs"></slot>
                </template>

                <template v-slot:footer>
                    <slot name="footer"></slot>
                </template>

            </c-grid-table>

        </div>
        <mugen-scroll :handler="fetchItems" :should-handle="!loading" :handle-on-mount="true">
            loading...
        </mugen-scroll>
    </div>

</template>

<script>
import MugenScroll from 'vue-mugen-scroll'

export default {
    name: "CInfiniteScrollIterator",
    components: {MugenScroll},
    props: {
        paginator: {
            required: true,
            type: Object
        },
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
        perPage: {
            required: false,
            type: Number,
            default: 5
        }
    },
    data() {
        return {
            loading: false,
            items: [],
            nextPageToLoad: 1
        }
    },
    methods: {
        fetchItems() {
            this.loading = true;
            this.$inertia.get(this.getPageUrl(this.nextPageToLoad), {}, {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => this.loading = false,
                onSuccess: () => {
                    this.items = this.items.concat(this.paginator.data);
                    this.nextPageToLoad = this.nextPageToLoad + 1;
                }
            })
        },
        getPageUrl(page) {
            let url = new URL(this.paginator.path);
            url.searchParams.set(this.pageAttributeName, page);
            url.searchParams.set(this.perPageAttributeName, this.perPage);
            return url.toString();
        }
    }
}
</script>

<style scoped>

</style>
