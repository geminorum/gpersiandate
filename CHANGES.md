### 3.5.7
* main/core: early load textdomain
* main/adminbar: clock on last
* main/format: check rtl for monthday
* main/calendar: padding args into link builder
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
