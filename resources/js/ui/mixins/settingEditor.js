export default {

    data() {
        return {
            settingValue: null
        }
    },

    mounted() {
        this.settingValue = this.$setting[this.settingKey];
    },

    methods: {
        saveSetting() {
            if(this.$setting[this.settingKey] !== this.settingValue) {
                this.$settings.setValue(this.settingKey, this.settingValue);
            }
        }
    }

}
