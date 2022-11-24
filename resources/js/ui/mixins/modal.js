export default {
    props: {
        value: {
            required: false,
            type: Boolean,
            default: null
        }
    },
    data() {
        return {
            overriddenModalValue: false
        }
    },
    computed: {
        showDialog: {
            get() {
                if(this.value === null) {
                    return this.overriddenModalValue;
                }
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
                this.overriddenModalValue = val;
            }
        }
    },
}
