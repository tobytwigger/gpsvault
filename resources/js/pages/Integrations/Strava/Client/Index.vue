<template>
    <c-app-wrapper title="Manage Strava Clients" :action-sidebar="true">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-own">
                <v-badge
                    :color="tab === 'tab-own' ? 'primary' : 'secondary'"
                    :content="ownedClients.total ? ownedClients.total : '0'"
                >
                    Your clients
                </v-badge>
            </v-tab>
            <v-tab href="#tab-shared">
                <v-badge
                    :color="tab === 'tab-shared' ? 'primary' : 'secondary'"
                    :content="sharedClients.total ? sharedClients.total : '0'"
                >
                    Shared with you
                </v-badge>
            </v-tab>
            <v-tab href="#tab-public">
                <v-badge
                    :color="tab === 'tab-public' ? 'primary' : 'secondary'"
                    :content="publicClients.total ? publicClients.total : '0'"
                >
                    Public
                </v-badge>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-own">
                <c-pagination-iterator :paginator="ownedClients" item-key="id" page-attribute-name="owned_page" per-page-attribute-name="owned_per_page">
                    <template v-slot:default="{item}">
                        <c-strava-client-card :client="item" type="owned"></c-strava-client-card>
                    </template>
                </c-pagination-iterator>
            </v-tab-item>

            <v-tab-item value="tab-shared">
                <c-pagination-iterator :paginator="sharedClients" item-key="id" page-attribute-name="shared_page" per-page-attribute-name="shared_per_page">
                    <template v-slot:default="{item}">
                        <c-strava-client-card :client="item" type="shared"></c-strava-client-card>
                    </template>
                </c-pagination-iterator>
            </v-tab-item>

            <v-tab-item value="tab-public">
                <c-pagination-iterator :paginator="publicClients" item-key="id" page-attribute-name="public_page" per-page-attribute-name="public_per_page">
                    <template v-slot:default="{item}">
                        <c-strava-client-card :client="item" type="public"></c-strava-client-card>
                    </template>
                </c-pagination-iterator>
            </v-tab-item>
        </v-tabs-items>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <c-strava-client-form title="Add new client" button-text="Create">
                        <template v-slot:activator="{trigger, showing}">
                            <v-btn
                                data-hint="You can add a new client here"
                                @click="trigger"
                                :disabled="showing"
                            >
                                Create client
                            </v-btn>
                        </template>
                    </c-strava-client-form>

                </v-list-item>
            </v-list>
        </template>
    </c-app-wrapper>
</template>

<script>
import CPaginationIterator from 'ui/components/CPaginationIterator';
import CStravaClientCard from 'ui/components/Strava/CStravaClientCard';
import CAppWrapper from '../../../../ui/layouts/CAppWrapper';
import CStravaClientForm from '../../../../ui/components/Strava/CStravaClientForm';
export default {
    name: "Index",
    components: {CStravaClientForm, CAppWrapper, CStravaClientCard, CPaginationIterator},
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
            tab: null
        }
    }
}
</script>

<style scoped>

</style>
