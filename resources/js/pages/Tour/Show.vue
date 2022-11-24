<template>
    <c-app-wrapper :title="tour.name ? tour.name : 'New Tour'" :menu-items="menuItems">
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
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <div v-if="tour.notes">
                                    {{ tour.notes }}
                                </div>
                                <div v-else>
                                    No notes
                                </div>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <c-activity-location-summary :started-at="tour.human_started_at" :ended-at="tour.human_ended_at"></c-activity-location-summary>
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
                </v-row>
                <v-row>
                    <v-col class="pa-8">
                        <c-tour-map :tour="tour"></c-tour-map>
                    </v-col>
                </v-row>
            </v-tab-item>

            <v-tab-item value="tab-stages">
                <v-container>
                    <v-row v-if="tour.stages.length === 0">
                        <v-col>
                            <c-stage-wizard :tour="tour"></c-stage-wizard>
                        </v-col>
                    </v-row>
                    <v-row v-else>
                        <v-col>
                            <c-stage-summary :tour="tour">

                            </c-stage-summary>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col>
                            <v-timeline dense>
                                <v-slide-x-reverse-transition
                                    group
                                    hide-on-leave
                                >
                                    <v-timeline-item
                                        :key="stage.id"
                                        v-for="stage in tour.stages"
                                        small
                                        fill-dot
                                    >
                                        <c-stage-card :stage="stage"></c-stage-card>
                                    </v-timeline-item>
                                </v-slide-x-reverse-transition>
                            </v-timeline>

                        </v-col>
                    </v-row>
                </v-container>

            </v-tab-item>
        </v-tabs-items>

        <c-tour-form :old-tour="tour" title="Edit tour" button-text="Update" v-model="showingTourEditForm"></c-tour-form>
        <c-delete-tour-button :tour="tour" v-model="showingTourDeleteForm"></c-delete-tour-button>

    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CStageForm from 'ui/components/Stage/CStageForm';
import CStageTable from 'ui/components/Stage/CStageTable';
import CPaginationIterator from 'ui/components/CPaginationIterator';
import CStageCard from 'ui/components/Stage/CStageCard';
import CTourMap from 'ui/components/Tour/CTourMap';
import CDeleteTourButton from 'ui/components/Tour/CDeleteTourButton';
import CTourForm from '../../ui/components/Tour/CTourForm';
import CActivityLocationSummary from '../../ui/components/CActivityLocationSummary';
import CStageWizard from '../../ui/components/Stage/CStageWizard';
import CStageSummary from '../../ui/components/Stage/CStageSummary';

export default {
    name: "Show",
    components: {
        CStageSummary,
        CStageWizard,
        CActivityLocationSummary,
        CTourForm,
        CDeleteTourButton, CTourMap, CStageCard, CPaginationIterator, CStageTable, CStageForm, CAppWrapper},
    props: {
        tour: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tab: null,
            showingTourEditForm: false,
            showingTourDeleteForm: false
        }
    },
    computed: {
        menuItems() {
            return [
                {
                    title: 'Edit tour details',
                    icon: 'mdi-pencil',
                    action: () => {
                        this.showingTourEditForm = true;
                    }
                },
                {
                    title: 'Delete tour',
                    icon: 'mdi-delete',
                    action: () => {
                        this.showingTourDeleteForm = true;
                    }
                },
            ]
        }
    }
}
</script>

<style scoped>

</style>
