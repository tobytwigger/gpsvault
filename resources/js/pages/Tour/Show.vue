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
                    <v-col :sm="12" :md="6">
                        <v-list flat>
                            <v-list-item v-if="tour.description">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-text
                                            </v-icon>
                                        </template>
                                        <span>Tour description</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-content>
                                        <div>
                                            {{ tour.description }}
                                        </div>
                                    </v-list-item-content>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="tour.notes">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-information-outline
                                            </v-icon>
                                        </template>
                                        <span>Tour notes</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="tour.notes"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="tour.marked_as_started_at">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-calendar
                                            </v-icon>
                                        </template>
                                        <span>Tour started at</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="tour.marked_as_started_at"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="tour.human_started_at && tour.human_ended_at">
                                <v-list-item-icon>
                                    <v-tooltip left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on">
                                                mdi-map-marker
                                            </v-icon>
                                        </template>
                                        <span>Route start/end points</span>
                                    </v-tooltip>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <c-activity-location-summary :started-at="tour.human_started_at" :ended-at="tour.human_ended_at"></c-activity-location-summary>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                    <v-col>
                        <c-stats :schema="statsSchema" :selectable="false"></c-stats>
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
                            <v-timeline dense>

                                <v-slide-x-reverse-transition
                                    group
                                    hide-on-leave
                                >
                                    <v-timeline-item style="text-align: center;" key="initial-stage-button" small hide-dot>
                                        <c-add-stage-button :tour="tour" :new-number="1"></c-add-stage-button>
                                    </v-timeline-item>

                                    <div :key="stage.id" v-for="(stage, stageIndex) in tour.stages">
                                        <v-timeline-item :color="(stage.activity_id ? 'success' : 'primary')" :key="stage.id + '-stage-card'" small fill-dot>
                                            <template v-slot:icon>
                                                <v-icon style="color: white;" v-if="stage.is_rest_day">mdi-sleep</v-icon>
                                                <v-avatar style="color: white;" v-else>{{stage.stage_number}}</v-avatar>
                                            </template>
                                            <c-stage-card :stage="stage"></c-stage-card>
                                        </v-timeline-item>

                                        <v-timeline-item style="text-align: center;" small hide-dot :key="stage.id + '-stage-button'">
                                            <c-add-stage-button :tour="tour" :new-number="stageIndex + 2"></c-add-stage-button>
                                        </v-timeline-item>
                                    </div>
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
import CPaginationIterator from 'ui/reusables/table/CPaginationIterator';
import CStageCard from 'ui/components/Stage/CStageCard';
import CTourMap from 'ui/components/Tour/CTourMap';
import CDeleteTourButton from 'ui/components/Tour/CDeleteTourButton';
import CTourForm from '../../ui/components/Tour/CTourForm';
import CActivityLocationSummary from '../../ui/components/CActivityLocationSummary';
import CStageWizard from '../../ui/components/Stage/CStageWizard';
import CStageSummary from '../../ui/components/Stage/CStageSummary';
import CStats from '../../ui/components/CStats';
import units from '../../ui/mixins/units';
import CAddStageButton from '../../ui/components/Stage/CAddStageButton';

export default {
    name: "Show",
    mixins: [units],
    components: {
        CAddStageButton,
        CStats,
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
        statsSchema() {
            let schema = [];
            if(this.tour.distance) {
                schema.push({
                    icon: 'mdi-ruler',
                    title: 'Distance',
                    label: 'distance',
                    disabled: true,
                    data: [
                        {value: this.convert(this.tour.distance, 'distance'), label: 'total'},
                    ]
                });
            }
            if(this.tour.elevation_gain) {
                schema.push({
                    icon: 'mdi-image-filter-hdr',
                    title: 'Elevation',
                    label: 'elevation',
                    pointLabel: 'elevation',
                    disabled: true,
                    data: [
                        {value: this.convert(this.tour.elevation_gain, 'elevation'), label: 'gain'},
                    ]
                })
            }
            schema.push({
                icon: 'mdi-calendar-today',
                title: 'Days Summary',
                label: 'days',
                disabled: true,
                data: [
                    {value: {
                        value: this.tour.stages.length,
                        unit: this.tour.stages.length === 1 ? 'day' : 'days'
                    }, label: 'total'},
                    {value: {
                        value: this.tour.stages.filter(s => s.is_rest_day === false).length,
                        unit: this.tour.stages.filter(s => s.is_rest_day === false).length === 1 ? 'day' : 'days'
                    }, label: 'cycling'},
                    {value: {
                        value: this.tour.stages.filter(s => s.is_rest_day === false && s.activity_id !== null).length,
                        unit: this.tour.stages.filter(s => s.is_rest_day === false && s.activity_id !== null).length === 1 ? 'day' : 'days'
                    }, label: 'completed'},
                ]
            })
            return schema;
        },
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
