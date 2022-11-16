<template>
    <c-confirmation-dialog :title="'Deleting place ' + place.name" button-text="Delete" :loading="loading" cancel-button-text="Nevermind" @confirm="deletePlace">
        <template v-slot:activator="{trigger,showing}">
            <slot name="button" v-bind="{trigger: trigger, showing: showing}">
                <v-btn
                    color="error"
                    @click="trigger"
                    :loading="showing"
                    :disabled="showing"
                >
                    <v-icon>mdi-delete</v-icon>
                    Delete Place
                </v-btn>
            </slot>
        </template>
        <p>Are you sure you want to delete the place '{{place.name}}'?</p>
    </c-confirmation-dialog>
</template>

<script>
import CConfirmationDialog from '../CConfirmationDialog';
export default {
    name: "CDeletePlaceButton",
    components: {CConfirmationDialog},
    props: {
        place: {
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
        deletePlace() {
            this.showDialog = false;
            this.loading = true;
            this.$inertia.delete(route('place.destroy', [this.place.id]), {
                onFinish: () => this.loading = false
            });
        }
    }
}
</script>

<style scoped>

</style>
