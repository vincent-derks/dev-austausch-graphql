=== WP REST Menus ===
Contributors: skapator
Tags: wp-rest-api, v2, api, wp-rest-menus, wp-api-menus, json-rest-api, menu-api-routes, menus, REST, wp-api, 
Requires at least: 4.4
Tested up to: 5.1
Requires PHP: 5.6
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add menus endpoints to WP REST API

== Description ==

This plugin adds new endpoints for WordPress registered menus.

The new endpoints available:

**Get all menus**
`
GET /menus/v1/menus

// Response sample
{  
    term_id: 2,
    name: "Main Menu",
    slug: "main-menu",
    term_group: 0,
    term_taxonomy_id: 2,
    taxonomy: "nav_menu",
    description: "",
    parent: 0,
    count: 8,
    filter: "raw"
},
...
`

**Get a menus items by id (term_id)**
`
GET /menus/v1/menus/<id>

// Response sample
{  
    ID: 5,
    post_author: "1",
    post_date: "2018-07-03 06:42:18",
    post_date_gmt: "2018-07-03 06:42:18",
    filter: "raw",
    db_id:5,
    menu_item_parent: "0",
    object_id: "5",
    object: "custom",
    type: "custom",
    type_label: "Custom Link",
    title: "Home",
    url: "https:\/\/wp-rest-menu.local\/",
    target: "",
    attr_title: "",
    description: "",
    classes: [  
     ""
    ],
    xfn: "",
    meta: null
},
...
`

**Get all menu locations**
All menu locations assigned  in /wp-admin/nav-menus.php?action=locations
`
GET /menus/v1/menus/locations

// Response sample
{  
    slug: "top",
    description: "Top Menu"
},
{  
    slug: "social",
    description: "Social Links Menu"
}
...
`

**Get all menu location items**
All menu locations assigned  in /wp-admin/nav-menus.php?action=locations
`
GET /menus/v1/menus/locations/<slug>

// Response sample
{  
    ID: 5,
    post_author: "1",
    post_date: "2018-07-03 06:42:18",
    post_date_gmt: "2018-07-03 06:42:18",
    filter: "raw",
    db_id:5,
    menu_item_parent: "0",
    object_id: "5",
    object: "custom",
    type: "custom",
    type_label: "Custom Link",
    title: "Home",
    url: "https:\/\/wp-rest-menu.local\/",
    target: "",
    attr_title: "",
    description: "",
    classes: [  
     ""
    ],
    xfn: "",
    meta: null
},
...
`

There are two filters availiable:

**Fields Filter**
`
// it will return only the fields specified
GET /menus/v1/menus/<id>/?fields=ID,title,meta

// Response sample
// Response sample
{  
    ID: 5,
    title: "Home",
    meta: null
},
...
`

**Nested Items Filter**
`
// it will return menu items parents and nested children in a 'children' field
// Currently only one level deep is supported
GET /menus/v1/menus/<id>/?nested=1

// Response sample
{  
  ID: 1716,
  menu_item_parent: "0",
  object_id: "174",
  object: "page",
  title: "Level 1",
  meta: {  
     vue_component: "LevelComponent",
     menu-item-field-01: "Field 1 value",
     menu-item-field-02: "Field 2 value"
  },
  children:[  
     {  
        ID: 1717,
        menu_item_parent: "1716",
        object_id: "744",
        object: "page",
        title: "Level 2b",
        meta : {  
           vue_component: null
        }
     },
     ...
  ]
},
...
`

**WP filter hooks**
There are two filter hooks availiable

`
add_filter( 'skap_wp_rest_menu_items', 'my_rest_menu_items', 10, 1 );

function my_rest_menu_items( $menu_items ) {
    // do something with $menu_items array
    return $menu_items;
}
`

`
add_filter( 'skap_wp_rest_menu_item_fields', 'my_rest_menu_item_fields', 10, 1 );

function my_rest_menu_item_fields( $fields ) {
    // You can modify the $fields array so
    // you can filter the return fields for all endpoints
    // without using the url param ?fields
    
    $fields = array( 'ID', 'title' );
    return $fields;
}
`

Supports custom fields and Advanced Custom Fields
All items include a meta field which contains all custom fields.

== Installation ==

There are no requirements other than Wordpress and one active menu. Installation is simple:

1. Upload the `wp-rest-menus` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I use this plugin? =

It creates endpoints for wp nav menus to use in your front end.

= Can I contribute? =

Yes, you can fork it on [github](https://github.com/skapator/wp-rest-menus).
