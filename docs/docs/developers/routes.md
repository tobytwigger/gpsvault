# Routes

A route is a planned path you can upload to a cycle computer. 

## Upload a route

If you upload a route from a file, much the same as an activity, we will create a row in the route table, then call `$route->analyse()`. This will dispatch the `AnalyseRouteFile` job, which will run the file through the analyser to get all the relevant information, then create a new linestring.

## Route database structure

With an activity, we keep a single list of points representing the activity. With a route, we want to be able to see the history of the route and so we create a 'route path'. We create a new route path attached to the route, with the linestring, distance and elevation gain.

For any points of interest along the route, we can also attach these to a route path. This doesn't apply when uploading a route path file though. A point along the route path can be associated with a 'place', or just be a stand alone point.


