<template>
    <c-app-wrapper title="Manage Strava Clients" :action-sidebar="true">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-own">Your clients<v-icon>mdi-heart-pulse</v-icon></v-tab>
            <v-tab href="#tab-shared">Shared with you<v-icon>mdi-heart-pulse</v-icon></v-tab>
            <v-tab href="#tab-public">Public<v-icon>mdi-heart-pulse</v-icon></v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-own">
                <c-pagination-iterator :paginator="ownedClients" item-key="id">
                    <template v-slot:default="{item}">
                        <c-strava-client-card :client="item"></c-strava-client-card>
                    </template>
                </c-pagination-iterator>
            </v-tab-item>

            <v-tab-item value="tab-shared">
                <c-pagination-iterator :paginator="sharedClients" item-key="id">
                    <template v-slot:default="{item}">
                        <c-strava-client-card :client="item"></c-strava-client-card>
                    </template>
                </c-pagination-iterator>
            </v-tab-item>

            <v-tab-item value="tab-public">
                <c-pagination-iterator :paginator="publicClients" item-key="id">
                    <template v-slot:default="{item}">
                        <c-strava-client-card :client="item"></c-strava-client-card>
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
                                data-intro="You can add a new client here"
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
    name: "Index.vue",
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
