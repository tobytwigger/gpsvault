let createDataset = (property, override, rawData) => {
    let dataset = {
        ...{
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        },
        ...override
    };
    dataset.data = rawData.map(p => {
        return {x: p.time * 1000, y: p[property]}
    })
    return dataset;
}

let availableDataSets = {
    elevation: {label: 'Elevation (m)'},
    cadence: {label: 'Cadence (rpm)'},
    temperature: {label: 'Temperature (C)'},
    heart_rate: {label: 'Heart Rate (bpm)', borderColor: 'rgb(255, 0, 0)'},
    speed: {label: 'Speed (km/h)'},
    grade: {label: 'Grade (%)'},
    battery: {label: 'Batter (%)'},
    calories: {label: 'Calories'}
};

export const createDatasets = (rawData, charts) => {
    return new Promise((resolve, reject) => {
        let availableData = ['elevation', 'cadence', 'temperature', 'heart_rate', 'speed', 'grade', 'battery', 'calories']
            .filter(property => charts.indexOf(property) > -1)
            .filter((property) => rawData.filter(d => d.hasOwnProperty(property) && d[property] !== null).length > 0)
        resolve({
            datasets: availableData
                .filter(property => availableDataSets.hasOwnProperty(property))
                .map(property => createDataset(property, availableDataSets[property],  rawData))
        });
    });
}
