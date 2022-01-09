<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog"  v-bind:showing="showDialog">

        </slot>

        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    {{ title }}
                </v-card-title>
                <v-card-text>
                    <slot></slot>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                        :disabled="loading"
                    >
                        {{ cancelButtonText }}
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="$emit('confirm')"
                        :loading="loading"
                        :disabled="loading"
                    >
                        {{buttonText }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "CConfirmationDialog",
    props: {
        title: {
            required: true,
            type: String
        },
        buttonText: {
            required: true,
            type: String
        },
        loading: {
            required: false,
            type: Boolean,
            default: false
        },
        cancelButtonText: {
            required: false,
            type: String,
            default: 'Cancel'
        }
    },
    data() {
        return {
            showDialog: false
        }
    },
    methods: {
        triggerDialog() {
            this.showDialog = true;
        }
    }
}
</script>

<style scoped>

</style>
