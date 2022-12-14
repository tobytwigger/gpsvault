<template>
    <c-app-wrapper title="Places" :header-action="true">
        <c-iterator :api-route="route('api.place.index')" item-key="id" :infinite-scroll="true">
            <template v-slot:default="{item}">
                <c-place-card :place="item"></c-place-card>
            </template>
        </c-iterator>

        </c-pagination-iterator>

        <template #headerActions>
            <c-place-form title="Add new place" button-text="Create">
                <template v-slot:activator="{trigger, showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                data-hint="You can add a new place here"
                                icon
                                v-bind="attrs"
                                v-on="on"
                                @click="trigger"
                                :disabled="showing"
                            >
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Add place</span>
                    </v-tooltip>
                </template>
            </c-place-form>

        </template>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CPlaceCard from 'ui/components/Place/CPlaceCard';
import CPlaceForm from 'ui/components/Place/CPlaceForm';
import CPaginationIterator from 'ui/reusables/table/CPaginationIterator';
export default {
    name: "Index",
    components: {CPaginationIterator, CPlaceForm, CPlaceCard, CAppWrapper},
    data() {
        return {
            test: {lat: null, lng: null}
        }
    }
}
</script>

<style scoped>

</style>
