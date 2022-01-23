<template>
    <c-app-wrapper title="Your Tours" :header-action="true">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-planned">Planned<v-icon>mdi-information</v-icon></v-tab>
            <v-tab href="#tab-completed">Completed<v-icon>mdi-information</v-icon></v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-planned">
                <c-pagination-iterator :paginator="tours" item-key="id">
                    <template v-slot:default="{item}">
                        <c-tour-card :tour="item"></c-tour-card>
                    </template>
                </c-pagination-iterator>
            </v-tab-item>

            <v-tab-item value="tab-completed">
                Completed
            </v-tab-item>
        </v-tabs-items>

        <template #headerActions>
            <c-tour-form title="Add new tour" button-text="Create">
                <template v-slot:activator="{trigger, showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                data-intro="You can add a new tour by clicking here"
                                icon
                                v-bind="attrs"
                                v-on="on"
                                @click="trigger"
                                :disabled="showing"
                            >
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Add tour</span>
                    </v-tooltip>
                </template>
            </c-tour-form>

        </template>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
import CPaginationIterator from '../../ui/components/CPaginationIterator';
import CTourCard from '../../ui/components/Tour/CTourCard';
import CTourForm from '../../ui/components/Tour/CTourForm';
export default {
    name: "Index",
    components: {CTourForm, CTourCard, CPaginationIterator, CAppWrapper},
    props: {
        tours: {
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
