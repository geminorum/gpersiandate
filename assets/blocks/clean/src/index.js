const { registerBlockType } = wp.blocks;
const { ServerSideRender } = wp.components;
const { _x } = wp.i18n;

registerBlockType('gpersiandate/clean', {
  title: _x('Clean Archives', 'Blocks: Clean', 'gpersiandate'),
  description: _x('Displays a clean monthly archive.', 'Blocks: Clean', 'gpersiandate'),
  keywords: [
    _x('clean', 'Blocks', 'gpersiandate'),
    _x('monthly', 'Blocks', 'gpersiandate'),
    _x('archive', 'Blocks', 'gpersiandate')
  ],
  category: 'widgets',
  icon: 'menu-alt',
  supports: {
    customClassName: false,
    multiple: false,
    reusable: false
  },
  edit: function (props) {
    return (
      <ServerSideRender
        block='gpersiandate/clean'
        attributes={props.attributes}
        // className={props.className}
      />
    );
  },
  save: function (props) {
    return null;
  }
});
