<template>
    <c-app-wrapper title="Places" :header-action="true">
        <c-pagination-iterator :paginator="places" item-key="id">
            <template v-slot:default="{item}">
                <c-place-card :place="item"></c-place-card>
            </template>
        </c-pagination-iterator>

        <template #headerActions>
            <c-place-form title="Add new place" button-text="Create">
                <template v-slot:activator="{trigger, showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                data-intro="You can add a new place here"
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
import CPaginationIterator from 'ui/components/CPaginationIterator';
import CLocationInput from '../../ui/components/CLocationInput';
export default {
    name: "Index",
    components: {CLocationInput, CPaginationIterator, CPlaceForm, CPlaceCard, CAppWrapper},
    props: {
        places: {
            required: true,
            type: Object
        },
    },
    data() {
        return {
            test: {lat: null, lng: null}
        }
    }
}
</script>

<style scoped>

</style>
