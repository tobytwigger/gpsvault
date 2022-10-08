export default {
  name: 'Heading',

  props: {
    align: {
      type: String,
      default () {
        return this.heading.align
      },
    },
  },

  computed: {
    justify () {
      switch (this.align) {
        case 'center': return 'center'
        case 'right': return 'end'
        default: return 'start'
      }
    },
  },
}
