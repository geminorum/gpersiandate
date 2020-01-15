const { registerBlockType } = wp.blocks;
const { ServerSideRender } = wp.components;
const { _x } = wp.i18n;

registerBlockType('gpersiandate/compact', {
  title: _x('Compact Archives', 'Blocks: Compact', 'gpersiandate'),
  description: _x('Displays a compact monthly archive.', 'Blocks: Compact', 'gpersiandate'),
  keywords: [
    _x('compact', 'Blocks', 'gpersiandate'),
    _x('monthly', 'Blocks', 'gpersiandate'),
    _x('archive', 'Blocks', 'gpersiandate')
  ],
  category: 'widgets',
  icon: 'archive',
  supports: {
    customClassName: false,
    multiple: false,
    reusable: false
  },
  edit: function (props) {
    return (
      <ServerSideRender
        block='gpersiandate/compact'
        attributes={props.attributes}
        // className={props.className}
      />
    );
  },
  save: function (props) {
    return null;
  }
});
