<template>
<!--        <v-img-->
<!--            v-if="stage.cover_image"-->
<!--            :src="stage.cover_image"-->
<!--            height="200px"-->
<!--        ></v-img>-->

        <v-card
            class="mx-auto"
        >
            <v-img
                v-if="stage.activity || stage.route"
                height="250"
                :src="stage.activity?.cover_image ?? stage.route?.cover_image"
            ></v-img>

            <v-card-title>
                <v-tooltip bottom v-if="stage.activity_id !== null">
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon color="green" v-bind="attrs" v-on="on">mdi-check</v-icon>
                    </template>
                    Stage completed
                </v-tooltip>
                <v-tooltip bottom v-else-if="stage.is_rest_day">
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon color="primary" v-bind="attrs" v-on="on">mdi-sleep</v-icon>
                    </template>
                    Rest day
                </v-tooltip>
                <v-tooltip bottom v-else-if="stage.route_id !== null">
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon color="primary" v-bind="attrs" v-on="on">mdi-map-marker</v-icon>
                    </template>
                    Route planned
                </v-tooltip>
                <v-tooltip bottom v-else>
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon color="warning" v-bind="attrs" v-on="on">mdi-map-marker-alert</v-icon>
                    </template>
                    Route not planned
                </v-tooltip>
                &nbsp;
                {{ stage.name ?? 'Stage ' + stage.stage_number }}
                <span v-if="stage.is_rest_day">
                    &nbsp;- Rest Day
                </span>

            </v-card-title>

            <v-card-text>
                <v-row
                    align="center"
                    class="mx-0"
                >
                    <v-card-text>
                        <v-chip
                            v-if="distance"
                            outlined
                            class="ma-2"
                            color="indigo"
                        >
                            <v-icon left>
                                mdi-ruler
                            </v-icon>
                            {{ distance.value }}{{distance.unit}}
                        </v-chip>

                        <v-chip
                            v-if="elevationGain"
                            outlined
                            color="indigo"
                        >
                            <v-icon left>
                                mdi-image-filter-hdr
                            </v-icon>
                            {{ elevationGain.value }}{{elevationGain.unit}}
                        </v-chip>
                    </v-card-text>
                </v-row>

                <div>{{ stage.description }}</div>
            </v-card-text>

            <v-card-actions>
                <v-tooltip bottom v-if="stage.route_id">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            color="deep-purple lighten-2"
                            text
                            v-bind="attrs"
                            v-on="on"
                            @click="$inertia.get(route('route.show', stage.route_id))"
                        >
                            View Route
                        </v-btn>
                    </template>
                    View route '{{ stage.route.name }}'
                </v-tooltip>
                <c-link-route-button :stage="stage" v-else-if="!stage.is_rest_day"></c-link-route-button>


                <v-tooltip bottom v-if="stage.activity_id">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            color="deep-purple lighten-2"
                            text
                            v-bind="attrs"
                            v-on="on"
                            @click="$inertia.get(route('activity.show', stage.activity_id))"
                        >
                            View Activity
                        </v-btn>
                    </template>
                    View activity '{{ stage.activity.name }}'
                </v-tooltip>
                <c-link-activity-button :stage="stage" v-else-if="!stage.is_rest_day"></c-link-activity-button>

                <v-spacer></v-spacer>

                <c-is-rest-day-toggle :tour-id="stage.tour_id" :old-stage="stage">
                </c-is-rest-day-toggle>

                <c-stage-form :tour-id="stage.tour_id" :old-stage="stage" title="Edit stage" button-text="Update">
                    <template v-slot:activator="{trigger, showing}">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    link
                                    @click="trigger"
                                    :disabled="showing"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </template>
                            Edit
                        </v-tooltip>
                    </template>
                </c-stage-form>

                <c-delete-stage-button :stage="stage">
                    <template #button="{trigger, showing}">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    link
                                    @click="trigger"
                                    :disabled="showing"
                                    :loading="showing"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                            Delete
                        </v-tooltip>
                    </template>
                </c-delete-stage-button>
            </v-card-actions>
        </v-card>
</template>

<script>
import moment from 'moment';
import units from '../../mixins/units';
import CStageForm from './CStageForm';
import CConfirmationDialog from '../CConfirmationDialog';
import CDeleteStageButton from './CDeleteStageButton';
import CRouteSelect from '../Route/CRouteSelect';
import CQuickRoute from './CQuickRoute';
import CIsRestDayToggle from './CIsRestDayToggle';
import CLinkRouteButton from './CLinkRouteButton';
import CLinkActivityButton from './CLinkActivityButton';

export default {
    name: "CStageCard",
    components: {
        CLinkActivityButton,
        CLinkRouteButton,
        CIsRestDayToggle, CQuickRoute, CRouteSelect, CDeleteStageButton, CConfirmationDialog, CStageForm},
    mixins: [units],
    props: {
        stage: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            isDeleting: false
        }
    },
    methods: {
        deleteStage() {
            this.isDeleting = true;
            this.$inertia.delete(route('tour.stage.destroy', [this.stage.tour_id, this.stage.id]), {
                onFinish: () => this.isDeleting = false
            })
        },
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY');
        },
    },
    computed: {
        allStats() {
            return this.stage.route?.stats ?? [];
        },
        distance() {
            let distance = this.stage.route?.path?.distance
            return distance ? this.convert(distance, 'distance') : null;
        },
        elevationGain() {
            let elevationGain = this.stage.route?.path?.elevation_gain;
            return elevationGain ? this.convert(elevationGain, 'elevation') : null;
        }
    }
}
</script>

<style scoped>

</style>
