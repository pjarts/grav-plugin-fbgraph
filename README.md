# FBGraph Plugin

The **FBGraph** Plugin is a Facebook Graph API client for  [Grav CMS](http://github.com/getgrav/grav)

## Installation

Installing the FBGraph plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install fbgraph

This will install the FBGraph plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/fbgraph`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `fbgraph`. You can find these files on [GitHub](https://github.com/pj-arts/grav-plugin-fbgraph) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/fbgraph

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/fbgraph/fbgraph.yaml` to `user/config/plugins/fbgraph.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
route: /fbgraph # endpoint for ajax api requests

host: https://graph.facebook.com/v2.8/
app_id: 1234567890123456 # your app ID
secret: q1w2e3r4t5y6u7i8o9p0a1s2d3 # your secret

resources: # an array of configured resources
```

### Resource configuration
A `resource` defines an endpoint that you want to request through the graph API. Upon request the resource configuration is transformed into a URL query string. Below is an example of a resource configuration.

```yaml
...
resources:
    my_photos:
        page_id: me
        edge: /photos
        params:
            type: uploaded
            limit: 10
            fields:
                - source
                - key: images
                  value:
                      - width
                      - height
                      - source
```

The field parameter defines the fields to be returned from the API. It is possible to define fields for nested resources with the use of key/value pairs. The above configuration will result in the following query string `fields=source,images{width,height,source}`

Refer to the [Facebook Graph API Reference](https://developers.facebook.com/docs/graph-api/reference) for more information on how to form your queries


## Usage
Begin by overriding `app_id` and `secret` with the values for your Facebook app

### Twig
The following functions are available in your twig template files

**fbgraph(_array_ resource)**
_resource_ is an associative array (or parsed yaml config)
The function will send a request to Facebook's API and return the response as parsed JSON

### Ajax
All resources configured under `plugins.fbgraph.resources` can be accessed via the /fbgraph.json endpoint. It accepts the following parameters
- `resource`(_required_) the name of the resource
- `before` hash for the before cursor (previous page)
- `after` hash for the after cursor (next page)

**example**
`http://mygravsite.local.host/fbgraph.json?resource=my_photos&after=MA2ss72UD8g12jbfa`

## To Do

- POST and PUT requests?
