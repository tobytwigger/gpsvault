<template>
    <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                fab
                small
                v-bind="attrs"
                v-on="on"
            >
                <bruit-io v-bind:config.prop="bruitConfig">
                    <v-icon>mdi-bug</v-icon>
                </bruit-io>
            </v-btn>
        </template>
        <span>Leave Feedback</span>
    </v-tooltip>
</template>

<script>
export default {
    name: "CFeedback",
    computed: {
        bruitConfig() {
            return {
                apiKey: this.$page.props.settings.bruit_api_key,
                labels: {
                    title: 'Any feedback?',
                    introduction: 'If you have any feedback, suggestions or have found any issues, let us know!',
                    button: 'Send'
                },
                form: [
                    {
                        label: 'Feedback',
                        type: 'textarea',
                        required: true
                    }, {
                        id: 'agreement',
                        type: 'checkbox',
                        label: 'I agree to send technical information in addition to my answers. This will help us with debugging any issues.',
                        value: null
                    }
                ]
            }
        }
    }
}
</script>

<style scoped>

</style>
