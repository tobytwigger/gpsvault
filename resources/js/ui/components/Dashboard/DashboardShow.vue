<template>
    <div>
        <h1>{{schema.name}}</h1>
        <small>{{schema.description}}</small>

        <div v-for="widget of schema.widgets">
            <component :is="widget.component" v-bind="widget.data"></component>
        </div>
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
    }
}
</script>

<style scoped>

</style>
