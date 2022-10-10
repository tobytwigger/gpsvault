<template>
    <widget
        :widget-name="widgetName"
        :data="data"
        :description="description"
        >
    </widget>
</template>

<script>
import Widget from '../Widget';
import units from '../../../mixins/units';
export default {
    name: "WTotalMileage",
    mixins: [units],
    components: {Widget},
    props: {
        data: {
            required: true,
            type: String
        },
        widgetName: {
            required: true,
            type: String
        },
        description: {
            required: true,
            type: String
        }
    },
}
</script>

<style scoped>

</style>
