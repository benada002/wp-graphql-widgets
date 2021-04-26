# WPQraphQL Widgets (Queries)
This plugin registers for each active widget a new widget type which exposes all settings of the widget (and for some widgets a `rendered` field [more here](#rendered-field)) and implements the `WidgetInterface` which exposes the following fields.
```
  WidgetInterface {
    active
    databaseId
    html
    id
    idBase
    title
    type
  }
```
It also adds sidebar queries.
## Requirements
- [WPGraphQL](https://github.com/wp-graphql/wp-graphql)

## Installation
You can install and activate it just like any other WordPress plugin.

## Usage
### Widget Examples
```graphql
  widget(id: "nav_menu-3") {
    ... on WPNavMenuWidget {
      navMenu
      active
      databaseId
      html
      id
      idBase
    }
  }
```
```graphql
  widgets {
    edges {
      cursor
      node {
        type
        title
        idBase
        id
        html
        databaseId
        active
        sidebar {
          node {
            databaseId
            description
          }
        }
      }
    }
  }
```
#### Rendered field
For the `WPNavMenuWidget`, `WPMediaImageWidget` and `WPWidgetRecentComments` types there is also `rendered` field which is a connection to their WPGraphQL type. Here is an example which gives you the menu of a menu widget.
```graphql
  widget(id: "nav_menu-3") {
    databaseId
    id
    ... on WPNavMenuWidget {
      rendered {
        node {
          menuItems {
            nodes {
              label
              path
            }
          }
        }
      }
    }
  }
```

### Sidebar Examples
```graphql
  sidebar(sidebar: SIDEBAR) {
    afterSidebar
    afterTitle
    afterWidget
    beforeSidebar
    beforeTitle
    beforeWidget
    class
    databaseId
    description
    id
    name
    widgets {
      edges {
        cursor
        node {
          databaseId
        }
      }
    }
  }
```
```graphql
  sidebars {
    edges {
      cursor
      node {
        databaseId
        name
        id
        description
        class
        beforeWidget
        beforeSidebar
        beforeTitle
        afterWidget
        afterTitle
        afterSidebar
        widgets {
          nodes {
            databaseId
          }
        }
      }
    }
  }
```
