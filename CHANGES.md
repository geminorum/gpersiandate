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
