<template>


    <v-app-bar
        id="home-app-bar"
        app
        clipped-left
        elevation="1"
        elevate-on-scroll
        height="80"
    >
        <img
            :src="'/dist/images/public/logo-' + ($vuetify.theme.isDark ? 'dark' : 'light') + '.png'"
            class="mr-3 hidden-xs-only"
            style="max-width: 100px;"
            width="100%"
            @click="$inertia.visit(route('welcome'))"
        />

        <v-spacer></v-spacer>

        <div>
            <v-tabs
                class="hidden-sm-and-down"
                optional
                background-color="transparent"
                :value="tab"
            >
                <v-tab
                    v-for="(item, i) in items"
                    :key="i"
                    :exact="false"
                    @click="(item.hardUrl ? goto(item.url) : $inertia.visit(item.url))"
                    :ripple="false"
                    class="font-weight-bold"
                    min-width="96"
                    text
                >
                    {{ item.name }}
                </v-tab>
            </v-tabs>
        </div>

        <div v-if="$page.props.user || user">
            <v-btn
                class="font-weight-bold ml-6"
                color="#11c684"
                dark
                depressed
                @click="$inertia.visit(route('dashboard'))"
                rel="noopener"
                x-large
            >
                Dashboard
            </v-btn>
        </div>

        <div v-else>
            <v-btn
                class="font-weight-bold ml-6"
                color="#11c684"
                dark
                depressed
                @click="$inertia.visit(route('register'))"
                rel="noopener"
                x-large
            >
                Sign up
            </v-btn>
        </div>

<!--        <v-app-bar-nav-icon-->
<!--            class="hidden-md-and-up"-->
<!--            @click="drawer = !drawer"-->
<!--        />-->
    </v-app-bar>

<!--    <v-app-bar app clipped-left>-->
<!--        <v-spacer></v-spacer>-->

<!--        <v-toolbar-title>GPS Vault - {{title}}</v-toolbar-title>-->

<!--        <v-spacer></v-spacer>-->


<!--        &lt;!&ndash; &ndash;&gt;-->
<!--    </v-app-bar>-->

</template>

<script>

export default {
    name: "CPublicNavBar",
    props: {
        drawer: {
            required: true,
            type: Boolean
        },
        title: {
            required: true,
            type: String
        },
        user: {
            required: false,
            type: Object,
            default: null
        }
    },
    data() {
        return {
        };
    },
    methods: {
        goto(url) {
            window.location.replace(url);
        }
    },
    computed: {
        showingSidebar: {
            get() {
                return this.sidebar;
            },
            set(val) {
                this.$emit('update:sidebar', val)
            }
        },
        items() {
            let elements = [
                {name: 'Home', url: route('welcome')},
                {name: 'How-to guides', url: route('documentation'), hardUrl: true},
                {name: 'Contact', url: route('contact')}
            ];
            if(!this.$page.props.user && !this.user) {
                elements.push({name: 'Login', url: route('login')});
            }
            return elements;
        },
        tab: {
            get() {
                let item = this.items.filter(i => window.location.href.replace(/\/?$/, '/') === i.url.replace(/\/?$/, '/'));
                return item.length === 0
                    ? null
                    : this.items.indexOf(item[0]);
            }
        }
    }
}
</script>

<style lang="sass">
#home-app-bar
    .v-tabs-slider
        max-width: 24px
        margin: 0 auto

    .v-tab
        &::before
            display: none
</style>
