<template>
    <div>
        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <template v-slot:activator="{on, attrs}">
                <v-list-item>
<!--                    <v-list-item-icon>-->
<!--                        <v-icon></v-icon>-->
<!--                    </v-list-item-icon>-->
                    <v-list-item-content>
                        <v-list-item-title>Import #{{index}} - {{importedAt}}</v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-icon>

                        <v-btn
                            v-bind="attrs"
                            v-on="on"
                            icon>
                            <v-icon>mdi-eye</v-icon>
                        </v-btn>
                    </v-list-item-icon>
                </v-list-item>
            </template>
            <v-card>
                <v-card-title>
                    Import #{{index}} - {{importedAt}}
                </v-card-title>
                <v-card-text>
                    <v-tabs
                        v-model="tab"
                        centered
                        grow
                        icons-and-text
                    >
                        <v-tabs-slider></v-tabs-slider>
                        <v-tab href="#tab-activities">Activities<v-icon>mdi-bike</v-icon></v-tab>
                        <v-tab href="#tab-photos">Photos<v-icon>mdi-camera</v-icon></v-tab>
                    </v-tabs>

                    <v-tabs-items v-model="tab">
                        <v-tab-item value="tab-activities">
                            <v-expansion-panels>
                                <v-expansion-panel v-if="activityImports.imported.length > 0">
                                    <v-expansion-panel-header>
                                        Imported ({{activityImports.imported.length}}) <small>We added files to these activities</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in activityImports.imported">
                                                <a :href="route('activity.show', i.data.activity_id)">{{i.data.activity_name ?? route('activity.show', i.data.activity_id)}}</a>
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel v-if="activityImports.unmatched.length > 0">
                                    <v-expansion-panel-header>
                                        Unmatched ({{activityImports.unmatched.length}}) <small>We can't match these files to an activity</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in activityImports.unmatched">
                                                {{i.data.file_location}}
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel v-if="activityImports.duplicate.length > 0">
                                    <v-expansion-panel-header>
                                        Already imported ({{activityImports.duplicate.length}}) <small>There's already a file against this activity</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in activityImports.duplicate">
                                                <a :href="route('activity.show', i.data.duplicate_id)">{{i.data.activity_name ?? route('activity.show', i.data.duplicate_id)}}</a>
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel v-if="activityImports.exception.length > 0">
                                    <v-expansion-panel-header>
                                        Error ({{activityImports.exception.length}}) <small>Something went wrong uploading these files</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in activityImports.exception">
                                                {{i.data.filename }} - {{i.data.message}}
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel v-if="activityImports.unknown.length > 0">
                                    <v-expansion-panel-header>
                                        Unknown ({{activityImports.unknown.length}}) <small>We're not sure what happened here - get in touch for help!</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in activityImports.unknown">
                                                #{{i.id }} for {{i.type}}. {{i.message}}.
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-tab-item>




                        <v-tab-item value="tab-photos">
                            <v-expansion-panels>
                                <v-expansion-panel v-if="photoImports.imported.length > 0">
                                    <v-expansion-panel-header>
                                        Imported ({{photoImports.imported.length}}) <small>We added these photos</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in photoImports.imported">
                                                <a :href="i.data.file_id ? route('file.download', i.data.file_id) : '#'">Photo {{i.data.filename}}</a> for <a :href="route('activity.show', i.data.activity_id)">{{i.data.activity_name ?? route('activity.show', i.data.activity_id)}}</a>
                                            </li>
                                        </ul>

                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel v-if="photoImports.unmatched.length > 0">
                                    <v-expansion-panel-header>
                                        Unmatched ({{photoImports.unmatched.length}}) <small>We can't match these photos to an activity</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in photoImports.unmatched">
                                                {{i.data.file_location}}
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel v-if="photoImports.duplicate.length > 0">
                                    <v-expansion-panel-header>
                                        Already imported ({{photoImports.duplicate.length}}) <small>This photo is already uploaded to your activity.</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in photoImports.duplicate">
                                                <a :href="route('activity.show', i.data.activity_id)">{{i.data.activity_name ?? route('activity.show', i.data.activity_id)}}</a> - {{i.data.file_location}}
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel v-if="photoImports.exception.length > 0">
                                    <v-expansion-panel-header>
                                        Error ({{photoImports.exception.length}}) <small>Something went wrong uploading these photos</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in photoImports.exception">
                                                {{i.data.filename }} - {{i.data.message}}
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel v-if="photoImports.unknown.length > 0">
                                    <v-expansion-panel-header>
                                        Unknown ({{photoImports.unknown.length}}) <small>We're not sure what happened here - get in touch for help!</small>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="i in photoImports.unknown">
                                                #{{i.id }} for {{i.type}}. {{i.message}}.
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-tab-item>
                    </v-tabs-items>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                    >
                        Close
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    name: "CImportResult",
    props: {
        index: {
            required: true,
            type: Number
        },
        importModel: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            showDialog: false,
            tab: null
        }
    },
    computed: {
        importedAt() {
            return moment(this.importModel.created_at).format('lll')
        },
        activityImports() {
            let results = {
                unmatched: [],
                duplicate: [],
                exception: [],
                imported: [],
                unknown: []
            };

            this.importModel.strava_import_results.filter(res => res.type === 'activities')
                .forEach((singleImport) => {
                    let key = results.hasOwnProperty(singleImport.message) ? singleImport.message : 'unknown';
                    results[key].push(singleImport);
                });

            return results;
        },
        photoImports() {
            let results = {
                unmatched: [],
                duplicate: [],
                exception: [],
                imported: [],
                unknown: []
            };

            this.importModel.strava_import_results.filter(res => res.type === 'photos')
                .forEach((singleImport) => {
                    let key = results.hasOwnProperty(singleImport.message) ? singleImport.message : 'unknown';
                    results[key].push(singleImport);
                });

            return results;
        }
    }
}
</script>

<style scoped>

</style>
