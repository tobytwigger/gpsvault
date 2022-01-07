<template>
    <c-app-wrapper :title="activity.name">
        <template #content>
            <v-row>
                <v-col cols="12" lg="8" xl="10">
                    <v-sheet color="white" elevation="3" rounded width="100%">
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

                            <v-tab href="#tab-analysis">
                                Analysis
                                <v-icon>mdi-chart-areaspline-variant</v-icon>
                            </v-tab>

                            <v-tab href="#tab-social">
                                Social
                                <v-icon>mdi-account-group</v-icon>
                            </v-tab>

                            <v-tab href="#tab-files">
                                Files
                                <v-icon>mdi-file-document-multiple</v-icon>
                            </v-tab>
                        </v-tabs>

                        <v-tabs-items v-model="tab">
                            <v-tab-item value="tab-summary">
                                Basic Stats
                                Images being shown in a nicer slider
                                <c-map v-if="hasStats" :key="'map-' + stats.integration" :stats="stats"></c-map>
                                <c-image-gallery :images="images"></c-image-gallery>
                            </v-tab-item>
                            <v-tab-item value="tab-analysis">
                                Raw stats
                                Charts
                                Able to change the data source
                            </v-tab-item>
                            <v-tab-item value="tab-social">
                                Show comments, kudos, segments
                            </v-tab-item>
                            <v-tab-item value="tab-files">
                                <c-file-form-dialog :activity="activity" title="Upload a file" text="Upload a new file">
                                    <template v-slot:activator="{trigger,showing}">
                                        <v-btn
                                            color="secondary"
                                            @click.stop="trigger"
                                            :disabled="showing"
                                        >
                                            <v-icon>mdi-upload</v-icon>
                                            Upload file
                                        </v-btn>
                                    </template>
                                </c-file-form-dialog>
                                <c-file-manager :files="activity.files" :activity="activity"></c-file-manager>
                            </v-tab-item>
                        </v-tabs-items>
                    </v-sheet>
                </v-col>
                <v-col cols="12" md="4" xl="2" class="d-flex justify-lg-center">
                    <v-sheet color="white" elevation="3" rounded>
                        <v-list>
                            <v-list-item>
                                <c-delete-activity-button :activity="activity"></c-delete-activity-button>
                            </v-list-item>
                            <v-list-item>
                                <v-btn v-if="activity.activity_file_id" link :href="route('file.download', activity.activity_file_id)">
                                    Download activity file
                                </v-btn>
                                <c-upload-activity-file-button :activity="activity" v-else></c-upload-activity-file-button>
                            </v-list-item>
                            <v-list-item>
                                <v-btn v-if="activity.activity_file_id" link :href="route('activity.download', activity.id)">
                                    Download activity
                                </v-btn>
                            </v-list-item>
                        </v-list>
                    </v-sheet>
                </v-col>
            </v-row>
        </template>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/wrappers/CAppWrapper';
import CDeleteActivityButton from '../../ui/components/Activity/CDeleteActivityButton';
import CUploadActivityFileButton from '../../ui/components/Activity/CUploadActivityFileButton';
import CMap from '../../ui/components/CMap';
import CImageGallery from '../../ui/components/CImageGallery';
import CFileManager from '../../ui/components/Activity/CFileManager';
import CFileFormDialog from '../../ui/components/Activity/CFileFormDialog';

export default {
    name: "Show",
    components: {
        CFileFormDialog,
        CFileManager, CImageGallery, CMap, CUploadActivityFileButton, CAppWrapper,CDeleteActivityButton},
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tab: 'tab-summary',
            dataSource: 'php'
        }
    },
    computed: {
        files() {

        },
        images() {
            return this.activity.files.filter(file => file.mimetype.startsWith('image/'))
                .map(file => {
                    return {
                        alt: file.caption,
                        src: route('file.preview', file.id)
                    }
                });
        },
        hasStats() {
            return this.stats !== null;
        },
        dataSources() {
            return Object.keys(this.activity.stats);
        },
        activeDataSource() {
            if (this.activity.stats.length === 0) {
                return null;
            }
            if (this.dataSource && this.dataSources.indexOf(this.dataSource) > -1) {
                return this.dataSource;
            }
            if (Object.keys(this.activity.stats).length > 0) {
                return Object.keys(this.activity.stats)[0];
            }
            return null;
        },
        stats() {
            if (this.activeDataSource !== null && this.activity.stats.hasOwnProperty(this.activeDataSource)) {
                return this.activity.stats[this.activeDataSource];
            }
            return null;
        }
    }
}
</script>

<style scoped>

</style>
