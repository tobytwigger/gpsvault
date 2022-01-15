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
                <v-btn color="secondary" :disabled="stageForm.processing" :loading="stageForm.processing" @click="createStage">
                    Add a stage
                </v-btn>
                <v-row>
                    <v-col v-for="stage in tour.stages" cols="12" xl="3" md="4" sm="6">
                        <c-stage-card :stage="stage"></c-stage-card>
                    </v-col>
                </v-row>

            </v-tab-item>
        </v-tabs-items>

    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
import CStageForm from '../../ui/components/Stage/CStageForm';
import CStageTable from '../../ui/components/Stage/CStageTable';
import CPaginationIterator from '../../ui/components/CPaginationIterator';
import CStageCard from '../../ui/components/Stage/CStageCard';
export default {
    name: "Show",
    components: {CStageCard, CPaginationIterator, CStageTable, CStageForm, CAppWrapper},
    props: {
        tour: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tab: null,
            stageForm: this.$inertia.form({
                stage_number: null,
                tour_id: this.tour.id
            })
        }
    },
    methods: {
        createStage() {
            this.stageForm.post(route('stage.store'), {
                onSuccess: () => {
                    this.stageForm.reset();
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
