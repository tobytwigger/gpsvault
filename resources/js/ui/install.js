import GlobalMixin from 'ui/mixins/globalMixin';

const requireComponent = require.context(
    // The relative path of the components folder
    './',
    // Whether or not to look in subfolders
    true,
    // The regular expression used to match base component filenames. V*.vue
    /[A-Z]\w+\.(vue|js)$/
);

export default {
    install: function (Vue) {
        // Register components
        requireComponent.keys().forEach(fileName => {
            const componentConfig = requireComponent(fileName)
            const componentName = fileName.split('/').pop().split('.')[0]
            Vue.component(componentName, componentConfig.default || componentConfig)
        })

        Vue.mixin(GlobalMixin);


    }
}
