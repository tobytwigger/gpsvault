<template>
    <c-app-wrapper title="Manage Strava Clients" :action-sidebar="true">
        <c-pagination-iterator :paginator="ownedClients" item-key="id">
            <template v-slot:default="{item}">
                <c-strava-client-card :client="item"></c-strava-client-card>
            </template>
        </c-pagination-iterator>

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
    props: {
        ownedClients: {
            required: true,
            type: Object
        }
    },
    components: {CStravaClientForm, CAppWrapper, CStravaClientCard, CPaginationIterator}
}
</script>

<style scoped>

</style>
