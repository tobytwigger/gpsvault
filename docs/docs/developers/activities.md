# Activities

Activities are rides that you have completed in the past. 

## Activity Importer

The normal way to import an activity is through the ActivityController 'store' method. This will receive and store an activity file, and use the activity importer at `App\Services\ActivityImport\ActivityImporter` to import the activity with the given name. The end result of this is a row in the `activities` table with the `file_id` representing the gpx file for the ride, a name, and a user to link to. 

Having saved the activity, we can call `$activity->analyse()`. This will dispatch an `AnalyseActivityFile` job.

## Has Stats

You can read more about the analyser in the [relevant documentation](./analysis.md). Once we have the analysis result, this gets saved against the Stats model, which contains all the information you need to know about the activity. The benefit of this is we can get analysis from many different sources and easily compare them.

As well as a row in the 'stats' table, we also save each of the individual points, and the stats for that point (elevation, heart rate, speed, location etc). 
