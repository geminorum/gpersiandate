### 3.8.1
* block/clean: wp scripts updated
* block/compact: wp scripts updated
* main/archives: move to date objects
* main/date: better checks for timestamps
* main/links: getting partials from date object on permalinks
* main/plugin: more woocommerce hooks
* main/search: support for term queries
* main/translate: filter for formatting ordinal/words numbers
* main/translate: sanitize locale method
* main/wordpress: translate hooks revised
* module/date: check if date already is an object
* module/translate: avoid formatting non-numbers

### 3.8.0
* main/admin: disabling conversion on woo-commerce order edit page
* main/adminbar: js clock revised
* main/bbpress: avoid double convertion of numbers
* main/calendar: bailing if no posttypes
* main/calendar: core css class for the table
* main/calendar: internal callback for building links
* main/calendar: moving tfoot after tbody complying with html 5.1
* main/date: :new: switch to object conversion
* main/date: :warning: fixed early returning
* main/date: accept objects on conversions
* main/date: constant for disabling conversion
* main/date: filter translate numbers by format
* main/date: mini sanitizing string from inputs
* main/date: proper parsing of the inputs
* main/date: tidy up from/to object methods
* main/datetime: accepting datetime immutable
* main/datetime: alternative method for leap years
* main/datetime: avoid sanitizing timezones when not needed
* main/datetime: bailing if cannot make datetime object
* main/datetime: late check for timezone constant
* main/datetime: make datetime object method
* main/format: account for single time part formats
* main/format: using strict comparison on arrays
* main/picker: :warning: date picker disabled for now!
* main/picker: correct handling rtl styles
* main/picker: default format from options
* main/picker: defaults as separate method
* main/picker: extend from module core
* main/picker: separate method for enqueue styles
* main/picker: set calendar as defaults
* main/plugins: country locale for woocommerce
* main/plugins: initial support for woocommerce numbers
* main/plugins: moving bp/bb filters
* main/search: account for extra space inside parentheses
* main/shortcodes: :new: today in persian/hijri shortcodes
* main/timezone: using core method for timezone string
* main/utilities: correct handling rtl styles
* main/utilities: get locale in iso 639
* main/wordpress: :warning: fixed override today in hijri
* main/wordpress: only translate chars on the title

### 3.7.1
* main/admin: check for options before converting dates on media grid view
* main/wordpress: early check for iso formats on i18n dates
* main/wordpress: support for new `wp_date` filter

### 3.7.0
* main/admin: :new: persian date on media grid view
* main/buddypress: retry bp filters
* main/links: convert query on admin
* main/modulecore: initial api for blocks
* main/shortcodes: :new: clean/compact block types
* main/shortcodes: css class attr
* main/translate: avoid constants on params
* main/translate: legacy format for strings with entities
* main/translate: more args on number format filter
* main/translate: support for old filters
* main/translate: using php number format
* main/wordpress: rename params for date_i18n
* main/widgets: seperate widgets

### 3.6.2
* :up: min php 5.6.20
* module/format: gettext filters deprecated
* module/shortcodes: disable minify html for clean archives
* module/translate: check types on numbers
* module/translate: late check for constant

### 3.6.1
* main/admin: data calendar type on inputs
* main/links: avoid using % sign on title parts

### 3.6.0
* module/archives: proper handling titles
* module/archives: return instead of printing row template in clean list
* module/archives: strip inline styles from compact table
* module/calendar: next/prev data key for year/month
* module/calendar: prep titles as title attr
* module/calendar: skip empty data calendar
* module/core: checking for wp is installing disabled
* module/format: check for more formats
* module/links: correct query to check for dates
* module/links: proper hooking filters
* module/shortcodes: cache key based on filtered args
* module/shortcodes: passing context into the clean archives
* module/shortcodes: ttl as atts and can be filtered
* module/timeago: :up: 1.6.5
* module/timezone: get object helper
* module/translate: support for precent sign
* module/utilities: prep title/desc helpers
* module/wordpress: navigation help for placeholders

### 3.5.11
* main/core: postpone timezone/locale constants
* main/admin: hide settings in rest

### 3.5.10
* main/plugin: check for min php before bootstrap
* main/format: correct override string
* main/timeago: localized numbers only in persian

### 3.5.9
* main/core: bp/bbp include moved early
* main/date: skip conversion on time only formats
* main/format: more overrides
* main/wordpress: more core filters

### 3.5.8
* main/archives: refactoring methods
* main/core: postpone language loading after plugins
* main/shortcodes: :new: `[entry-link-published]`

### 3.5.7
* main/core: early load textdomain
* main/adminbar: clock on last
* main/format: check rtl for monthday
* main/calendar: passing args into link builder
* main/calendar: more html data attributes

### 3.5.6
* main/format: custom date formats filter
* main/wordpress: new filter for attachment caption

### 3.5.5
* lib/datetime: rethinking misc methods
* core/html: correct class for links
* main/admin: correct end time
* main/calendar: :new: rewrite!
* main/date: passing calendar into make time
* main/date: new method as getdate
* main/format: not overriding start of the week
* main/links: less calls to make date for archives links
* main/links: check for dep function
* main/links: using helper for string positions
* main/picker: stabilizing enqueue method
* main/shortcodes: minify & caching expensive results
* main/strings: abbreviations for months
* main/strings: hijri month names updated
* main/strings: :warning: correct order of week days
* main/timeago: :new: support for [jquery-timeago](https://github.com/rmm5t/jquery-timeago)
* main/timezone: timestamp conversion method
* main/wordpress: moved wordpress methods here

### 3.5.4
* main/archives: :warning: fixed fatal: correct class for strip clauses
* main/archives: :new: new compact archives
* main/archives: :new: new clean archives
* main/archives: attempt on get method
* main/date: first/last supports multiple posttypes
* main/date: first/last option for password protected
* main/date: additional wrappers for make time method
* main/date: days in month array as a method
* main/date: wrapper for to without number translations
* main/translate: :new: filtering attachment data
* main/format: giving up string replacements!
* main/shortcodes: :new: new module

### 3.5.3
* main/date: same wrapper method for all supporting calendars
* main/datetime: sanitize timezone/calendar
* main/datetime: support for datetime object
* main/format: diffrent format for rtl in gMember strings
* main/timezone: :warning: fixed fatal upon no timezone string available

### 3.5.2
* main/admin: support months dropdown for attachments
* main/format: static caching the l10n overrides
* main/wordpress: check for date token in menu items before

### 3.5.1
* main/picker: PersianDate updated to 0.7.1
* main/picker: English digits for non fa locale
* main/links: :warning: fixed fatal for protected method

### 3.5.0
* all: new core classes
* build/gulp: setting up
* main: plugin file constant
* main: textdomain moved in init func
* main/core: not registering admin only modules
* main/admin: using helper for first~last of the month
* main/admin: using helper for posttype months dropdown
* main/admin: screen reader label for months dropdown
* main/admin: using current screen filter for restrictions
* main/adminbar: rewriting adminbar clock
* main/links: revising strip date clauses
* main/links: revising day/month/year/post link conversions
* main/translate: more generic filters
* main/translate: fixed static method notices
* main/wordpress: filters for translating modified date/time, [see](https://core.trac.wordpress.org/ticket/37059)
* main/wordpress: filter document title
* main/worpress: check for post before conversion
* main/search: better handling arabic char/numbers
* main/strings: no need for the numeric keys
* main/format: rechecking overrides
* main/format: filtering [gMember](https://github.com/geminorum/gmember/) formats
* main/date: first~last posttype helper
* main/buddypress: removing old filters
* main/picker: :pray: new date picker

### 3.4.1
* strings: last x month helper, inspired from [Month Dropdown in PHP](http://paulferrett.com/2012/month-dropdown-in-php/)
* form: new module

### 3.4.0
* moved to [Semantic Versioning](http://semver.org/)
* buddypress: more filters
* strings: am/pm formatting
* strings: fewer calls using static variables
* strings: sanitize calendar helper
* format: more iso filtering

### 0.3.3
* strings: initial day of the week
* calendar: correct day of the week columns

### 0.3.2
* all: using exception on not loading the modules
* strings: fixed notice on day of the week

### 0.3.1
* format: more iso filtering
* archives: add support for post type
* widgets: updated to WP4.4
* strings: correct order of the day of the week
* strings: hijri days of the week

### 0.3.0
* complete rewrite

### 0.2.34
* using localized widget instead of the WP's

### 0.2.33
* providing localized version of P2's `get_js_locale()`
* correct order of day of the week strings

### 0.2.32
* seperating changelog into [CHANGES.md](CHANGES.md)

### 0.2.31
* support for [GitHub Updater](https://github.com/afragen/github-updater)

### 0.2.30
* date picker style for MP6 admin
* archives widget updated as wp core 4.1
* correct way of handling javascript enqueues
* cleanup the code

### 0.2.29
* first public release
