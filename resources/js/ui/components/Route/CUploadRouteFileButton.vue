<template>
    <v-dialog
        v-model="showDialog"
        persistent
        max-width="600"
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="secondary"
                v-bind="attrs"
                v-on="on"
                :loading="form.processing"
                :disabled="form.processing"
            >
                <v-icon>mdi-upload</v-icon>
                Upload route file
            </v-btn>
        </template>
        <v-card>
            <v-card-title>
                Upload route file
            </v-card-title>
            <v-card-text>
                Upload the gpx file for this route.
            </v-card-text>
            <v-card-text>
                <c-file-input
                    v-model="form.file"
                    id="route-file"
                    name="file"
                    label="Route file"
                    hint="Upload the gpx file for this route."
                    :error="form.errors.hasOwnProperty('file')"
                    :error-messages="form.errors.hasOwnProperty('file') ? [form.errors.file] : []"
                ></c-file-input>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="secondary"
                    @click="showDialog = false"
                >
                    Cancel
                </v-btn>
                <v-btn
                    color="primary"
                    @click="uploadRouteFile"
                >
                    Upload
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import CFileInput from '../CFileInput';
export default {
    name: "CUploadRouteFileButton",
    components: {CFileInput},
    props: {
        routeModel: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            showDialog: false,
            form: this.$inertia.form({
                file: null,
                _method: 'patch'
            })
        }
    },
    methods: {
        uploadRouteFile() {
            this.showDialog = false;
            this.form.post(route('route.update', this.routeModel.id), {
                onSuccess: () => {
                    this.form.reset();

                }
            });
        }
    }
}
</script>

<style scoped>

</style>
