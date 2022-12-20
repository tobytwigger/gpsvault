<template>
    <c-confirmation-dialog :title="'Deleting stage ' + stage.stage_number" button-text="Delete" :loading="loading" cancel-button-text="Nevermind" @confirm="deleteStage">
        <template v-slot:activator="{trigger,showing}">
            <slot name="button" v-bind="{trigger: trigger, showing: showing}">
                <v-btn
                    color="error"
                    @click="trigger"
                    :loading="showing"
                    :disabled="showing"
                >
                    <v-icon>mdi-delete</v-icon>
                    Delete Stage
                </v-btn>
            </slot>
        </template>
        <p>Are you sure you want to delete the stage '{{stage.name ? stage.name : 'Unnamed'}}'?</p>

        <p>The route will still be available, but you will lose information about the date and any other plans.</p>
    </c-confirmation-dialog>
</template>

<script>
import CConfirmationDialog from '../CConfirmationDialog';
export default {
    name: "CDeleteStageButton",
    components: {CConfirmationDialog},
    props: {
        stage: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            showDialog: false,
            loading: false
        }
    },
    methods: {
        deleteStage() {
            this.showDialog = false;
            this.loading = true;
            this.$inertia.delete(route('tour.stage.destroy', [this.stage.tour_id, this.stage.id]), {
                preserveScroll: true,
                onFinish: () => this.loading = false
            });
        }
    }
}
</script>

<style scoped>

</style>
