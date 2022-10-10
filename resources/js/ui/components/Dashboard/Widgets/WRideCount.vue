<template>
    <widget
        widget-name="Number of rides"
        :data="count.toString()"
        description="rides this year"
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
        count: {
            required: true,
            type: Number
        }
    },
}
</script>

<style scoped>

</style>
