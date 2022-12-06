<template>
    <div>
        <div v-if="layout === 'cards'">
            <c-grid-table :items="paginator.data"
                          :item-key="itemKey"
                          :loading="loading"
                          :prepend="prepend">

                <template v-slot:default="attrs">
                    <slot name="default" v-bind="attrs"></slot>
                </template>

                <template v-slot:footer>
                    <v-row
                        align="center"
                        class="mt-2 mx-4"
                        justify="center"
                    >
                        <span class="grey--text">Items per page</span>
                        <v-menu offset-y>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    class="ml-2"
                                    color="primary"
                                    dark
                                    text
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    {{ perPage }}
                                    <v-icon>mdi-chevron-down</v-icon>
                                </v-btn>
                            </template>
                            <v-list>
                                <v-list-item
                                    v-for="number in [2,4,6,8,10,12,14,16,18,20,25,30,40,50,100]"
                                    :key="number"
                                    @click="perPage = number"
                                >
                                    <v-list-item-title>{{ number }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>

                        <v-spacer></v-spacer>

                        <span
                            class="mr-4
            grey--text"
                        >
            Page {{ page }} of {{ paginator.last_page }}
          </span>
                        <v-btn
                            :disabled="page === 1"
                            class="mr-1"
                            color="blue darken-3"
                            dark
                            fab
                            @click="page = page - 1"
                        >
                            <v-icon>mdi-chevron-left</v-icon>
                        </v-btn>
                        <v-btn
                            :disabled="page === paginator.last_page"
                            class="ml-1"
                            color="blue darken-3"
                            dark
                            fab
                            @click="page = page + 1"
                        >
                            <v-icon>mdi-chevron-right</v-icon>
                        </v-btn>
                    </v-row>
                </template>

            </c-grid-table>
        </div>
    </div>

</template>

<script>
import CTable from './CTable';
import CGridTable from './CGridTable';

export default {
    name: "CPaginationIterator",
    components: {CGridTable, CTable},
    props: {
        paginator: {
            required: true,
            type: Object
        },
        itemKey: {
            required: true,
            type: String
        },
        prepend: {
            required: false,
            type: Boolean,
            default: false
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
        layout: {
            required: false,
            type: String,
            default: 'cards',
            validator: (val) => ['cards', 'list'].includes(val)
        },
        listHeaders: {
            required: false,
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            loading: false,
            activitiesByPage: {}
        }
    },
    computed: {
        page: {
            get() {
                return Number.parseInt(this.paginator.current_page)
            },
            set(page) {
                this.$inertia.get(this.getPageUrl(page, this.perPage), {
                    onFinish: () => this.loading = false
                });
            }
        },
        perPage: {
            get() {
                return Number.parseInt(this.paginator.per_page)
            },
            set(perPage) {
                this.loading = true;
                this.$inertia.get(this.getPageUrl(this.page, perPage), {
                    onFinish: () => this.loading = false
                });
            }
        },
    },
    methods: {
        getPageUrl(page, perPage) {
            let url = new URL(this.paginator.path);
            url.searchParams.set(this.pageAttributeName, page);
            url.searchParams.set(this.perPageAttributeName, perPage);
            return url.toString();
        }
    }
}
</script>

<style scoped>

</style>
