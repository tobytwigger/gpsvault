<template>
    <c-app-wrapper title="Tours">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-summary">Summary<v-icon>mdi-information</v-icon></v-tab>
            <v-tab href="#tab-stages">Stages<v-icon>mdi-map-marker-distance</v-icon></v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-summary">
                <v-row>
                    <v-col>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <div v-if="tour.description">
                                    {{ tour.description }}
                                </div>
                                <div v-else>
                                    No description
                                </div>

                                <div v-if="tour.notes">
                                    {{ tour.notes }}
                                </div>
                                <div v-else>
                                    No notes
                                </div>
                            </v-col>
                        </v-row>
                        <v-row v-if="tour.marked_as_started_at">
                            <v-col>
                                <v-chip class="ma-2">
                                    <v-icon>mdi-calendar</v-icon>
                                    {{ tour.marked_as_started_at }}
                                </v-chip>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col>
                        Total distance, total elevation etc.
<!--                        <c-stats v-if="hasStats" :schema="statSchema" :limit="4"></c-stats>-->
<!--                        <div v-else>No stats available</div>-->
                    </v-col>
                </v-row>
                <v-row>
                    <v-col class="pa-8">
                        Map
<!--                        <c-activity-map v-if="hasStats" :key="'map-' + stats.integration" :stats="stats"></c-activity-map>-->
                    </v-col>
                </v-row>
            </v-tab-item>

            <v-tab-item value="tab-stages">
                <c-stage-form button-text="Add Stage" title="Add a new stage" :tour-id="tour.id">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn color="secondary" @click="trigger" :disabled="showing">
                            Add a stage
                        </v-btn>
                    </template>
                </c-stage-form>
                <c-stage-table :stages="tour.stages"></c-stage-table>
            </v-tab-item>
        </v-tabs-items>

    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
import CStageForm from '../../ui/components/Stage/CStageForm';
import CStageTable from '../../ui/components/Stage/CStageTable';
export default {
    name: "Index",
    components: {CStageTable, CStageForm, CAppWrapper},
    props: {
        tour: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tab: null
        }
    },
}
</script>

<style scoped>

</style>
