import CryptoJS from 'crypto-js';

export default {
    data() {
        return {
            duplicateRoute: null,
            checkingDuplicate: false,
        }
    },
    computed: {
        duplicateMessage() {
            if(this.duplicateRoute !== null) {
                return 'This route is a duplicate of ' + ziggyRoute('route.show', this.duplicateRoute.id) + '. You can still upload it if you would like.';
            }
            return null;
        }
    },
    methods: {
        checkForDuplicateRoute() {
            let reader = new FileReader();
            let self = this;
            reader.addEventListener('load',function (event) {
                self.checkingDuplicate = true;
                let hash = CryptoJS.MD5(CryptoJS.enc.Latin1.parse(event.target.result)).toString();
                axios.post(ziggyRoute('route.file.duplicate'), {hash: hash})
                    .then(response => self.duplicateRoute = response.data.route)
                    .catch(error => self.duplicateRoute = null)
                    .then(() => self.checkingDuplicate = false);
            });
            reader.readAsBinaryString(this.file);
        },
    }
}
