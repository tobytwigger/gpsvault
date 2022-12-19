export default {
    methods: {
        getIconUrl(type) {
            let icon = {
                food_drink: 'food_drink',
                shops: 'basket',
                toilets: 'toilets',
                water: 'water',
                tourist: 'eiffel-tower',
                accommodation: 'accommodation',
                other: ''
            }[type] ?? null;
            if(icon) {
                return '/dist/images/map/' + icon + '.svg';
            }
            return null;
        },
        getColor(type, otherwise = 'info') {
            return {
                food_drink: 'amber lighten-1',
                shops: 'blue-grey lighten-2',
                toilets: 'teal lighten-3',
                water: 'blue lighten-1',
                tourist: 'light-green lighten-1',
                accommodation: 'deep-orange lighten-1',
                other: 'grey lighten-2'
            }[type] ?? otherwise;
        }
    }
}
