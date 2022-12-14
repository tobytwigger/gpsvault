<template>
    <c-app-wrapper title="Your Activities">
        <c-iterator :api-route="route('api.activity.index')" item-key="id" :list-headers="['Name', 'Distance', 'Date', 'View']"
                  layout="cards" :infinite-scroll="true">
            <template v-slot:default="{item, isFirst}">
                <c-activity-card :activity="item" :hints="isFirst"></c-activity-card>
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
            <c-job-status job="load-strava-activities" :tags="{user_id: $page.props.user.id}">
                <template v-slot:incomplete>
                    <v-progress-circular
                        indeterminate
                        color="primary"
                    ></v-progress-circular>
                </template>
            </c-job-status>

            <c-activity-form title="Add new activity" button-text="Create">
                <template v-slot:activator="{trigger, showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                id="tour-newActivityButton"
                                icon
                                v-bind="attrs"
                                v-on="on"
                                @click="trigger"
                                :disabled="showing"
                            >
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Add activity</span>
                    </v-tooltip>
                </template>
            </c-activity-form>

        </template>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CActivityCard from 'ui/components/Activity/CActivityCard';
import CActivityForm from 'ui/components/Activity/CActivityForm';
import CPaginationIterator from 'ui/reusables/table/CPaginationIterator';
import CJobStatus from '../../ui/components/CJobStatus';
import units from '../../ui/mixins/units';
import moment from 'moment/moment';
import shepherd from '../../ui/mixins/shepherd';
import CInfiniteScrollIterator from '../../ui/reusables/table/CInfiniteScrollIterator';
import CIterator from '../../ui/reusables/table/CIterator';
export default {
    name: "Index",
    components: {
        CIterator,
        CInfiniteScrollIterator, CJobStatus, CPaginationIterator, CActivityForm, CActivityCard, CAppWrapper},
    mixins: [units, shepherd],
    data() {
        return {
            tourSteps: [
                this._createStep('#tour-newActivityButton', 'You can add a new activity here', 'left'),
                this._createStep('.tour-viewSingleActivityButton', 'Click to view an activity', 'top'),
            ],
        }
    },
    methods: {
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY');
        }
    }
}
</script>

<style scoped>

</style>
