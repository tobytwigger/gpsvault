<template>
    <div>
        <v-btn
            class="align-self-end"
            fab
            icon
            outlined
            x-small
            :color="$vuetify.theme.dark ? 'white' : 'black'"
            :disabled="stageForm.processing"
            :loading="stageForm.processing"
            @click="createStage"
        >
            <v-icon>mdi-plus</v-icon>
        </v-btn>
    </div>

</template>

<script>
export default {
    name: "CAddStageButton",
    props: {
        tour: {
            required: true,
            type: Object
        },
        newNumber: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            stageForm: this.$inertia.form({
                stage_number: this.newNumber,
            })
        }
    },
    methods: {
        createStage() {
            this.stageForm.stage_number = this.newNumber;
            this.stageForm.post(route('tour.stage.store', this.tour.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.stageForm.reset();
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
