<template>
    <widget
        widget-name="Total Time"
        :data="processedTime"
        description="of riding this year"
        >
    </widget>
</template>

<script>
import Widget from '../Widget';
import units from '../../../mixins/units';
import moment from 'moment';
export default {
    name: "WTotalMileage",
    mixins: [units],
    components: {Widget},
    props: {
        duration: {
            required: true,
            type: Number
        }
    },
    computed: {
        processedTime() {
            return this.duration / (60 * 60) + ' hours';
        }
    }
}
</script>

<style scoped>

</style>
