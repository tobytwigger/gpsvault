import * as convert from 'convert-units'

export default {
    install(app, options) {
        app.mixin({
            methods: {
                $converter(value, from) {
                    let converted = convert(value).from(from).toBest({system: 'metric'});
                    return converted.val + ' ' + converted.unit;
                }
            }
        });
    }
}
