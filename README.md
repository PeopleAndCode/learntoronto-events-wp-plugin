# LearnToronto Events WP-Plugin

This plugin grabs event data from LearnToronto.org v1 api (http://learntoronto.org/api/v1/), and generates a new ````learntoronto_event```` custom post type.

## LearnToronto.org API structure

````js
{
  events: [                       // Array of event objects
    {
      id: Integer,                // LearnToronto Event ID
      name: String,               // Event Name
      start_time: DateTime UTC,   // Start Time and date of event
      url: String,                // url to event's websitem
      description: String,        // Text containing HTML
      updated_at: DateTimeUTC,    // Event info updated at Time UTC
      group: String               // Name of the Group/Organization that is organizing/owns the event
      venue: {                    // JSON object representing the Venue info/address
        name: String,             // Name of venue
        address_1: String,        // Address Line 1 of venue - typically the street number and name
        address_2: String,        // Address Line 2 of venue - typically suite number or other
        city: String,             // City of venue
        state: String,            // Province/State of venue
        zip: String,              // Postal/Zip Code of venue
        country: String           // Country Name/Code of venue
      }
    }
  ]
}
````

## Custom Post Type

The ````learntoronto_event```` post type is created by ````<plugin_dir>\post-types\learntoronto-event-posttype.php````.  

The following shows the mapping of Wordpress loop functions to the corresponding API values upon creation/update:

1. WP Function ````the_content()```` maps to ````description````
2. WP Function ````the_title()```` maps to ````name````

These are added upon create/update actions except for ````the_title()````.


## Plugin Postmeta fields

All postmeta fields are prefixed with ````learntoronto_event_```` followed by their respective attribute names.

### Examples

postmeta field ````learntoronto_event_id```` maps to the event's ````id```` attribute.
postmeta field ````learntoronto_event_url```` maps to the event's ````url```` attribute.

### Exceptions

1. **Description** does not map to a postmeta field.  Instead it is saved as the custom ````learntoronto_event```` post type's ````post_content```` and can be called with ````the_content()```` call inside of the Wordpress loop.

2. **Venue** maps into 8 distinct postmeta fields.  Each field has the prefix ````learntoronto_event_venue_```` followed by the venue's attribute name.

    1. ````venue: {name:}```` maps to ````learntoronto_event_venue_name````
    2. ````venue: {address_1:}```` maps to ````learntoronto_event_venue_address_1````
    3. ````venue: {address_2:}```` maps to ````learntoronto_event_venue_address_2````
    4. ````venue: {city:}```` maps to ````learntoronto_event_venue_city````
    5. ````venue: {state:}```` maps to ````learntoronto_event_venue_state````
    6. ````venue: {zip:}```` maps to ````learntoronto_event_venue_zip````
    7. ````venue: {country:}```` maps to ````learntoronto_event_venue_country````
    8. **Special** ````learntoronto_event_venue_full_address```` is the concatenated value for all ````venue```` object attribute values.

Example: ````venue: { name: }```` maps to ````learntoronto_event_venue_name````.  the 8th distinct postmeta field for venue is the ````learntoronto_event_full_address``` postmeta field which concatenates the venue object values to a single string - which is used for things like maps APIs/searches.

## Updates

Updates are performed at 4am each day.  This is registered with Wordpresses' built-in "cron" system.