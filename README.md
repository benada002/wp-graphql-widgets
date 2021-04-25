# WPQraphQL Widgets (Queries)
This plugin adds widgets and sidebar queries to WPGraphQL.

## Requirements
- [WPGraphQL](https://github.com/wp-graphql/wp-graphql)

## Installation
You can install and activate it just like any other WordPress plugin.

## Usage
### Widget Examples
```
  widget(id: "widget_instance_id") {
    active
    databaseId
    html
    id
    idBase
    title
    type
    sidebar {
      node {
        name
        databaseId
      }
    }
  }
```
```
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
For the `WPNavMenuWidget`, `WPMediaImageWidget` and `WPWidgetRecentComments` types there is also `rendered` field which is a connection to their WPGraphQL type. Here is an example.
```
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
```
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
```
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
