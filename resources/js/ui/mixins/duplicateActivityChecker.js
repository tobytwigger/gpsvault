import CryptoJS from 'crypto-js';

export default {
    data() {
        return {
            duplicateActivity: null,
            checkingDuplicate: false,
        }
    },
    computed: {
        duplicateMessage() {
            if(this.duplicateActivity !== null) {
                return 'This activity is a duplicate of ' + ziggyRoute('activity.show', this.duplicateActivity.id) + '. You can still upload it if you would like.';
            }
            return null;
        }
    },
    methods: {
        checkForDuplicateActivity() {
            let reader = new FileReader();
            let self = this;
            reader.addEventListener('load',function (event) {
                self.checkingDuplicate = true;
                let hash = CryptoJS.MD5(CryptoJS.enc.Latin1.parse(event.target.result)).toString();
                axios.post(ziggyRoute('activity.file.duplicate'), {hash: hash})
                    .then(response => self.duplicateActivity = response.data.activity)
                    .catch(error => self.duplicateActivity = null)
                    .then(() => self.checkingDuplicate = false);
            });
            reader.readAsBinaryString(this.file);
        },
    }
}
