<template>
    <v-dialog
        v-model="showDialog"
        persistent
        max-width="600"
    >
        <v-card>
            <v-card-title>
                Deleting route '{{routeModel.name}}'.
            </v-card-title>
            <v-card-text>
                Are you sure you want to delete the route '{{routeModel.name}}'? Once the route is deleted, all of its resources and
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
                    @click="deleteRoute"
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
    name: "CDeleteRouteButton",
    mixins: [modal],
    props: {
        routeModel: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            loading: false
        }
    },
    methods: {
        deleteRoute() {
            this.showDialog = false;
            this.loading = true;
            this.$inertia.delete(route('route.destroy', this.routeModel.id), {
                onFinish: () => this.loading = false
            });
        }
    }
}
</script>

<style scoped>

</style>
