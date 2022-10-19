<template>
    <div>
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title class="justify-center">
                    <span>Stage Wizard</span>
                </v-card-title>

                <v-card-text>
                    <v-input
                        type="number"
                        id="count"
                        v-model="form.count"
                        label="How many days is your tour?"
                        hint="How many days will you be doing your tour, including rest days."
                        name="count"
                        prepend-icon="mdi-calendar-question"
                        :error="form.errors.hasOwnProperty('count')"
                        :error-messages="form.errors.hasOwnProperty('count') ? [form.errors.email] : []"
                    ></v-input>
                </v-card-text>
                <v-card-actions>
                    <v-btn :loading="form.processing" :disabled="form.processing" aria-label="Create stages" block color="primary"
                           type="submit">
                        <v-icon>mdi-arrow-right</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </div>
</template>

<script>
export default {
    name: "CStageWizard",
    data() {
        return {
            form: this.$inertia.form({
                count: 3
            })
        }
    },
    methods: {
        submit() {
            this.form.post(route('tour.stage.store', this.tour.id), {
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
