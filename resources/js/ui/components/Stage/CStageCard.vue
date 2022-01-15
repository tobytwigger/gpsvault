<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >
<!--        <v-img-->
<!--            v-if="stage.cover_image"-->
<!--            :src="stage.cover_image"-->
<!--            height="200px"-->
<!--        ></v-img>-->


        <v-card-title>
            Stage {{stage.stage_number}}<span v-if="stage.name"> - {{stage.name}}</span>
        </v-card-title>

        <v-card-subtitle v-if="stage.description">
            {{ stage.description }}
        </v-card-subtitle>

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

<!--            <v-list-item-avatar-->
<!--                tile-->
<!--                size="80"-->
<!--                color="grey"-->
<!--            ></v-list-item-avatar>-->
<!--        </v-list-item>-->

        <v-card-actions>
            <v-btn
                v-if="stage.route_id"
                color="deep-purple lighten-2"
                text
                @click="$inertia.get(route('route.show', stage.route_id))"
            >
                Route
            </v-btn>

            <v-btn
                v-if="stage.activity_id"
                color="deep-purple lighten-2"
                text
                @click="$inertia.get(route('activity.show', stage.activity_id))"
            >
                Activity
            </v-btn>

            <v-spacer></v-spacer>

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

            <c-confirmation-dialog title="Delete stage?" button-text="Delete" :loading="isDeleting" cancel-button-text="Nevermind" @confirm="deleteStage">
                <template v-slot:activator="{trigger,showing}">
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
                <p>Are you sure you want to delete this stage?</p>

                <p>The route will still be available, but you will lose information about the date and any other plans.</p>
            </c-confirmation-dialog>

        </v-card-actions>

    </v-card>
</template>

<script>
import moment from 'moment';
import units from '../../mixins/units';
import CStageForm from './CStageForm';
import CConfirmationDialog from '../CConfirmationDialog';
import routeStats from '../../mixins/routeStats';

export default {
    name: "CStageCard",
    components: {CConfirmationDialog, CStageForm},
    mixins: [units, routeStats],
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
            this.$inertia.delete(route('stage.destroy', this.stage.id), {
                onFinish: () => this.isDeleting = false
            })
        },
        convertDistance(value) {
            let converted = this.convert(value, 'distance');
            return converted.value + converted.unit;
        },
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY');
        },
    },
    computed: {
        hasStats() {
            return this.stage.route && Object.keys(this.stage.route.stats).length > 0;
        },
        stats() {
            return this.hasStats && this.stage.route.stats.hasOwnProperty('php') ? this.stage.route.stats.php : null
        }
    }
}
</script>

<style scoped>

</style>
