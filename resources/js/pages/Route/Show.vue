<template>
    <c-app-wrapper :title="route.name" :action-sidebar="true">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>

            <v-tab href="#tab-summary">
                Summary
                <v-icon>mdi-information</v-icon>
            </v-tab>

            <v-tab href="#tab-files">
                Files
                <v-icon>mdi-file-document-multiple</v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-summary">
                The description and notes, stats on the route - distance, elevation, time, cafe stops etc, and a map.
                {{ route }}
            </v-tab-item>
            <v-tab-item value="tab-files">
            </v-tab-item>
        </v-tabs-items>

        <template #sidebar>
            <v-list>
                Delete route
                Upload route file
                download route file
                edit route - name, desc, notes
<!--                <v-list-item>-->
<!--                    <c-delete-route-button :route="route"></c-delete-route-button>-->
<!--                </v-list-item>-->
<!--                <v-list-item v-if="!route.route_file_id">-->
<!--                    <c-upload-route-file-button :route="route"></c-upload-route-file-button>-->
<!--                </v-list-item>-->
<!--                <v-list-item v-if="route.route_file_id">-->
<!--                    <v-btn link :href="ziggyRoute('file.download', route.route_file_id)">-->
<!--                        Download route file-->
<!--                    </v-btn>-->
<!--                </v-list-item>-->
<!--                <v-list-item v-if="route.route_file_id">-->
<!--                    <v-btn link :href="ziggyRoute('route.download', route.id)">-->
<!--                        Download route-->
<!--                    </v-btn>-->
<!--                </v-list-item>-->
<!--                <v-list-item>-->
<!--                    <c-route-form :old-route="route" title="Edit route" button-text="Update">-->
<!--                        <template v-slot:activator="{trigger,showing}">-->
<!--                            <v-btn :disabled="showing" @click="trigger">-->
<!--                                Edit Route-->
<!--                            </v-btn>-->
<!--                        </template>-->
<!--                    </c-route-form>-->
<!--                </v-list-item>-->
<!--                <v-list-item>-->
<!--                    <v-select-->
<!--                        class="pt-2"-->
<!--                        v-model="activeDataSource"-->
<!--                        item-text="integration"-->
<!--                        item-value="integration"-->
<!--                        :items="allStats"-->
<!--                        hint="Choose which data sets to show"-->
<!--                        label="Data Source"-->
<!--                        dense-->
<!--                    ></v-select>-->
<!--                </v-list-item>-->
<!--                <v-list-item>-->
<!--                    <a :href="'https://www.strava.com/activities/' + route.additional_data.strava_id"-->
<!--                       v-if="route.linked_to.indexOf('strava') !== -1"-->
<!--                    >View on strava</a>-->
<!--                </v-list-item>-->
            </v-list>
        </template>
    </c-app-wrapper>
</template>

<script>
import moment from 'moment';
import CAppWrapper from '../../ui/layouts/CAppWrapper';

export default {
    name: "Show",
    components: {CAppWrapper},
    props: {
        route: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tab: 'tab-summary'
        }
    },
    methods: {
        formatDateTime(dt) {
            return moment(dt).format('DD/MM/YYYY HH:mm:ss');
        }
    },
    computed: {
    }
}
</script>

<style scoped>

</style>
