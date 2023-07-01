import {MapMarkerPopup, MapMarkerPopupButton, MapState} from "./types/MapType";
import {v4 as uuidv4} from 'uuid';

// Popups

function createPopup(): PopupFactory {
    return new PopupFactory();
}

class PopupFactory {
    buttons: MapMarkerPopupButton[] = [];
    private title: string|null = null;
    private location: { latitude: number; longitude: number } = {latitude: 0, longitude: 0};

    addButton(text: string, onClick: () => void): PopupFactory {
        this.buttons.push(
            createButton(text, onClick)
        )

        return this;
    }

    withTitle(title: string|null): PopupFactory {
        this.title = title;

        return this;
    }

    create(): MapMarkerPopup {
        let popup: MapMarkerPopup = {
            uniqueId: uuidv4(),
            title: this.title,
            buttons: this.buttons
        }
        if(this.location) {
            popup.coordinates = this.location;
        }
        return popup;
    }

    setLocation(location: { latitude: number; longitude: number }) {
        this.location = location;

        return this;
    }
}

// Buttons

function createButton(text: string, onClick: () => void): MapMarkerPopupButton {
    return {
        label: text,
        action: onClick,
        uniqueId: uuidv4()
    }
}

// General State

function createState(): MapState {
    return {
        popups: {},
        bounds: {
            southWest: {lat: -1, lng: 2},
            northEast: {lat: 1, lng: 10},
        },
        zoom: 1,
        markers: {}
    }
}

export {createPopup, createState};
