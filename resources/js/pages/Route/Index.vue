<template>
    <c-app-wrapper title="Your Routes" :header-action="true">
        <c-pagination-iterator :paginator="routes" item-key="id">
            <template v-slot:default="{item}">
                <c-route-card :route-model="item"></c-route-card>
            </template>
        </c-pagination-iterator>

        <template #headerActions>
            <c-route-form title="Upload a Route" button-text="Upload">
                <template v-slot:activator="{trigger, showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                data-hint="You can upload a route file here."
                                icon
                                v-bind="attrs"
                                v-on="on"
                                @click="trigger"
                                :disabled="showing"
                            >
                                <v-icon>mdi-upload</v-icon>
                            </v-btn>
                        </template>
                        <span>Upload route</span>
                    </v-tooltip>
                </template>
            </c-route-form>

            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        data-hint="You can add a new route by clicking here"
                        icon
                        :href="route('planner.create')"
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>
                <span>Add route</span>
            </v-tooltip>

        </template>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CPaginationIterator from 'ui/components/CPaginationIterator';
import CRouteCard from 'ui/components/Route/CRouteCard';
import CRouteForm from 'ui/components/Route/CRouteForm';

export default {
    name: "Index",
    components: {CRouteForm, CRouteCard, CPaginationIterator, CAppWrapper},
    props: {
        routes: {
            required: true,
            type: Object
        }
    }
}
</script>

<style scoped>

</style>
