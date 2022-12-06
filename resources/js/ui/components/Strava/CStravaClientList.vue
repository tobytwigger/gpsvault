<template>
    <div>
        <!--    <v-badge-->
        <!--                :color="tab === 'tab-own' ? 'primary' : 'secondary'"-->
        <!--                :content="ownedClients.total ? ownedClients.total : '0'"-->
        <!--            >-->
        <!--                Your clients-->
        <!--            </v-badge>-->
        <!--            <v-badge-->
        <!--                :color="tab === 'tab-shared' ? 'primary' : 'secondary'"-->
        <!--                :content="sharedClients.total ? sharedClients.total : '0'"-->
        <!--            >-->
        <!--                Shared with you-->
        <!--            </v-badge>-->
        <!--            <v-badge-->
        <!--                :color="tab === 'tab-public' ? 'primary' : 'secondary'"-->
        <!--                :content="publicClients.total ? publicClients.total : '0'"-->
        <!--            >-->
        <!--                Public-->
        <!--            </v-badge>-->

        <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    icon
                    link
                    @click="reload"
                    :loading="isReloading"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon>mdi-autorenew</v-icon>
                </v-btn>
            </template>
            Reload data
        </v-tooltip>


        <c-pagination-iterator :paginator="ownedClients" item-key="id" page-attribute-name="owned_page"
                               per-page-attribute-name="owned_per_page">
            <template v-slot:default="{item}">
                <c-strava-client-card :client="item" type="owned"></c-strava-client-card>
            </template>
        </c-pagination-iterator>

        <c-pagination-iterator :paginator="sharedClients" item-key="id" page-attribute-name="shared_page"
                               per-page-attribute-name="shared_per_page">
            <template v-slot:default="{item}">
                <c-strava-client-card :client="item" type="shared"></c-strava-client-card>
            </template>
        </c-pagination-iterator>

        <c-pagination-iterator :paginator="publicClients" item-key="id" page-attribute-name="public_page"
                               per-page-attribute-name="public_per_page">
            <template v-slot:default="{item}">
                <c-strava-client-card :client="item" type="public"></c-strava-client-card>
            </template>
        </c-pagination-iterator>
    </div>
</template>

<script>
import CPaginationIterator from 'ui/reusables/table/CPaginationIterator';
import CStravaClientCard from './CStravaClientCard';

export default {
    name: "CStravaClientList",
    components: {CStravaClientCard, CPaginationIterator},
    props: {
        ownedClients: {
            required: true,
            type: Object
        },
        sharedClients: {
            required: true,
            type: Object
        },
        publicClients: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tab: null,
            isReloading: false
        }
    },
    methods: {
        reload() {
            this.$inertia.reload({
                onStart: () => this.isReloading = true,
                onFinish: () => this.isReloading = false
            });
        }
    }
}
</script>

<style scoped>

</style>
