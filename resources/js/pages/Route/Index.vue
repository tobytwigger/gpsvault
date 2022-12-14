<template>
    <c-app-wrapper title="Your Routes" :header-action="true">
        <c-iterator :api-route="route('api.route.index')" item-key="id" :infinite-scroll="true">
            <template v-slot:default="{item, isFirst}">
                <c-route-card :route-model="item"></c-route-card>
            </template>
            <template v-slot:list="{item, isFirst}">
                <td>{{item.name}}</td>
                <td>{{convert(item.distance, 'distance')['value']}} {{convert(item.distance, 'distance')['unit']}}</td>
                <td>
                    {{ toDateTime(item.started_at) }}
                </td>
                <td>
                    <v-btn @click="$inertia.visit(route('activity.show', item.id))" icon>
                        <v-icon>mdi-eye</v-icon>
                    </v-btn>
                </td>
            </template>
        </c-iterator>

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
import CPaginationIterator from 'ui/reusables/table/CPaginationIterator';
import CRouteCard from 'ui/components/Route/CRouteCard';
import CRouteForm from 'ui/components/Route/CRouteForm';

export default {
    name: "Index",
    components: {CRouteForm, CRouteCard, CPaginationIterator, CAppWrapper},
}
</script>

<style scoped>

</style>
