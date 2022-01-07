<template>
    <div>
        <label id="listbox-label" class="block text-sm font-medium text-gray-700">
            Data Source
        </label>
        <div>
            <select v-model="dynamicValue">
                <option v-for="source in dataSources" :value="source">{{ source }}</option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    name: "SelectDataSource",
    props: {
        dataSources: {
            required: true,
            type: Array
        },
        modelValue: {
            required: false,
            type: String,
            default: null
        }
    },
    computed: {
        dynamicValue: {
            get() {
                return this.modelValue;
            },
            set(val) {
                this.$emit('update:modelValue', val);
            }
        }
    }
}
</script>

<style scoped>

</style>
