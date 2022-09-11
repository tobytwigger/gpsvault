<template>
    <c-app-wrapper title="Your Activities" :header-action="true">
        <c-pagination-iterator :paginator="activities" item-key="id">
            <template v-slot:default="{item, isFirst}">
                <c-activity-card :activity="item" :hints="isFirst"></c-activity-card>
            </template>
        </c-pagination-iterator>

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
                                data-hint="You can add a new activity here"
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
import CPaginationIterator from 'ui/components/CPaginationIterator';
import CJobStatus from '../../ui/components/CJobStatus';
export default {
    name: "Index",
    components: {CJobStatus, CPaginationIterator, CActivityForm, CActivityCard, CAppWrapper},
    props: {
        activities: {
            required: true,
            type: Object
        }
    }
}
</script>

<style scoped>

</style>
