<template>
    <v-dialog
        v-model="showDialog"
        persistent
        max-width="600"
    >
        <v-card>
            <v-card-title>
                Deleting activity '{{activity.name}}'.
            </v-card-title>
            <v-card-text>
                Are you sure you want to delete the activity '{{activity.name}}'? Once the activity is deleted, all of its resources and
                data will be permanently deleted.
            </v-card-text>
            <v-card-text>
                If you haven't done so, make sure you have a recent backup before continuing.
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="secondary"
                    @click="showDialog = false"
                >
                    Nevermind
                </v-btn>
                <v-btn
                    color="error"
                    @click="deleteActivity"
                >
                    Delete
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import modal from '../../mixins/modal';

export default {
    name: "CDeleteActivityButton",
    mixins: [modal],
    props: {
        activity: {
            required: true,
            type: Object
        },
    },
    data() {
        return {
            loading: false
        }
    },
    methods: {
        deleteActivity() {
            this.showDialog = false;
            this.loading = true;
            this.$inertia.delete(route('activity.destroy', this.activity.id), {
                onFinish: () => this.loading = false
            });
        }
    }
}
</script>

<style scoped>

</style>
