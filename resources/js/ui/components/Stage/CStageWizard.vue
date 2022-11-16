<template>
    <div>
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title class="justify-center">
                    <span>Stage Wizard</span>
                </v-card-title>

                <v-card-text>
                    <v-text-field
                        type="number"
                        id="total_days"
                        v-model="form.total_days"
                        label="How many days is your tour?"
                        hint="How many days will you be doing your tour, including rest days."
                        name="total_days"
                        prepend-icon="mdi-calendar-question"
                        :error="form.errors.hasOwnProperty('total_days')"
                        :error-messages="form.errors.hasOwnProperty('total_days') ? [form.errors.email] : []"
                    ></v-text-field>
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
    props: {
        tour: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                total_days: 3
            })
        }
    },
    methods: {
        submit() {
            this.form.post(route('tour.stage.wizard', this.tour.id), {
                onSuccess: () => {
                    this.form.reset();
                    this.$inertia.reload();
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
