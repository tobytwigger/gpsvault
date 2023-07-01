import {MapMarker, MapMarkerPopup, MapMarkerPopupButton} from "./types/MapType";
import maplibregl from "maplibre-gl";

function convertMarker(marker: MapMarker, mapLibreMarker?: maplibregl.Marker): maplibregl.Marker {

    //     // Create the marker
        let markerEl = document.createElement('div');
        markerEl.id = marker.uniqueId;
        markerEl.className = 'marker clickable';
        markerEl.style.cursor = 'pointer';
        if(marker.backgroundImage !== undefined && marker.backgroundImage !== null) {
            markerEl.style.backgroundImage = marker.backgroundImage;
        }
        markerEl.style.width = '20px';
        markerEl.style.height = '48px';

        if(mapLibreMarker === undefined) {
            mapLibreMarker = new maplibregl.Marker({element: markerEl});
        }

        mapLibreMarker.setLngLat([marker.longitude, marker.latitude]);
        mapLibreMarker.setDraggable(marker.draggable ?? false);


        if(marker.popup !== undefined && marker.popup !== null) {
            mapLibreMarker.setPopup(convertMarkerPopup(marker.popup));
        }

        return mapLibreMarker;
}

function convertMarkerPopup(popup: MapMarkerPopup, existingPopup?: maplibregl.Popup): maplibregl.Popup {

    let buttonDiv = document.createElement('div');

    if(popup.title) {
        let titleSpan = document.createElement('span');
        titleSpan.innerHTML = popup.title;
        buttonDiv.appendChild(titleSpan);
    }

    for(let button of popup.buttons ?? []) {
        buttonDiv.appendChild(convertMarkerPopupButton(button));
    }


    if(existingPopup === undefined) {
        existingPopup = new maplibregl.Popup({ offset: 25 });
    }

    existingPopup.setDOMContent(buttonDiv);
    if(popup.coordinates) {
        existingPopup.setLngLat([popup.coordinates.longitude, popup.coordinates.latitude]);
    }

    return existingPopup;
}

function convertMarkerPopupButton(button: MapMarkerPopupButton): HTMLDivElement {
    let buttonElement = document.createElement('a');
    buttonElement.innerHTML = button.label;
    buttonElement.id = button.uniqueId;
    buttonElement.addEventListener('click', button.action);

    let container = document.createElement('div');
    container.style.padding = '3px';
    container.appendChild(buttonElement)

    return container;
}

export {convertMarker, convertMarkerPopup, convertMarkerPopupButton};
