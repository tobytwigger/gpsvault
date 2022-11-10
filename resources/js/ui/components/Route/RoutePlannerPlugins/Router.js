export default class {

    router(waypoints, callback, context, options) {
        let timedOut = false,
            wps = [],
            url,
            timer,
            wp,
            i;

        options = options || {};
        //waypoints = options.waypoints || waypoints;
        url = this.buildRouteUrl(waypoints, options);


        timer = setTimeout(function () {
            timedOut = true;
            callback.call(context || callback, {
                status: -1,
                message: 'OSRM request timed out.'
            });
        }, options.timeout);

        // Create a copy of the waypoints, since they
        // might otherwise be asynchronously modified while
        // the request is being processed.
        for (i = 0; i < waypoints.length; i++) {
            wp = waypoints[i];
            wps.push({
                latLng: wp.latLng,
                name: wp.name || "",
                options: wp.options || {}
            });
        }


        axios.get(url, L.bind(function (err, resp) {
            let data;

            clearTimeout(timer);
            if (!timedOut) {
                if (!err) {
                    data = JSON.parse(resp.responseText);
                    this._routeDone(data, wps, callback, context);
                } else {
                    console.log("Error : " + err.response);
                    callback.call(context || callback, {
                        status: err.status,
                        message: err.response
                    });
                }
            }
        }, this), true);

        return this;
    }

    buildRouteUrl(waypoints, options) {
        let locs = [],
            locationKey,
            hint;
        let transitM = options.transitmode || this._transitmode;
        let streetName = options.street;
        let costingOptions = this._costingOptions;
        this._transitmode = transitM;

        for (let i = 0; i < waypoints.length; i++) {
            let loc;
            locationKey = this._locationKey(waypoints[i].latLng).split(',');
            if(i === 0 || i === waypoints.length-1){
                loc = {
                    lat: parseFloat(locationKey[0]),
                    lon: parseFloat(locationKey[1]),
                    type: "break"
                }
            }else{
                loc = {
                    lat: parseFloat(locationKey[0]),
                    lon: parseFloat(locationKey[1]),
                    type: "through"
                }
            }
            locs.push(loc);
        }

        let params = JSON.stringify({
            locations: locs,
            costing: transitM,
            street: streetName,
            costing_options: costingOptions
        });

        return this.options.serviceUrl + 'route?json=' +
            params + '&api_key=' + this._accessToken;
    }

    buildElevationUrl(waypoints, options) {

    }
}
