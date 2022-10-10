<template>
    <div>
        <div class="text-right pr-5">
            <small>{{schema.description}}</small>
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
    mounted() {
        this.timeout = setTimeout(() => {
            this.$inertia.reload();
        }, this.schema.refresh_rate * 1000);
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
