<template>
    <div>
        <div class="text-right pr-5">
            <small>{{schema.description}}</small>

            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        icon
                        link
                        @click="$inertia.reload()"
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-autorenew</v-icon>
                    </v-btn>
                </template>
                Reload dashboard
            </v-tooltip>

        </div>

        <grid-layout
            style="height: 500px;"
            :layout="layout"
            :col-num="3"
            :row-height="30"
            :is-draggable="false"
            :is-resizable="false"
            :is-mirrored="false"
            :vertical-compact="true"
            :margin="[10, 10]"
            :use-css-transforms="true"
        >

            <grid-item v-for="widget in schema.widgets"
                       :x="widget.position.x"
                       :y="widget.position.y"
                       :w="widget.position.w"
                       :h="widget.position.h"
                       :i="widget.i"
                       :key="widget.i">
                <component :is="widget.component" v-bind="widget.data"></component>
            </grid-item>
        </grid-layout>
    </div>
</template>

<script>
export default {
    name: "DashboardShow",
    props: {
        schema: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            intervalId: null
        }
    },
    mounted() {
        this.intervalId = setInterval(() => {
            this.$inertia.reload();
        }, this.schema.refresh_rate * 1000);
    },
    beforeDestroy() {
        clearInterval(this.intervalId);
    },
    computed: {
        layout() {
            return this.schema.widgets.map(w => {
                return {
                    ...w.position,
                    i: w.id
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
