<template>
    <div>
        {{ tour.stages.length }} days
        <v-btn color="secondary" :disabled="stageForm.processing" :loading="stageForm.processing" @click="createStage">
            Add a day
        </v-btn>
    </div>

</template>

<script>
export default {
    name: "CStageSummary",
    props: {
        tour: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            stageForm: this.$inertia.form({
                stage_number: null,
            })
        }
    },
    methods: {
        createStage() {
            this.stageForm.post(route('tour.stage.store', this.tour.id), {
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
