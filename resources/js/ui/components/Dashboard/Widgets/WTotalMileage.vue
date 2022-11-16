<template>
    <widget
        :widget-name="widgetName"
        :data="processedDistance"
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
        distance: {
            required: true,
            type: Number
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
    computed: {
        processedDistance() {
            let distanceUnit = this.convert(this.distance, 'distance');
            return distanceUnit.value + distanceUnit.unit;
        }
    }
}
</script>

<style scoped>

</style>
