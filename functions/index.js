const functions = require('firebase-functions');
const admin = require('firebase-admin');
const { encodeGeohash } = require('./geohashService');

admin.initializeApp();

exports.updateDriverLocation = functions.database
    .ref('/drivers/{driverId}')
    .onUpdate((change, context) => {
        const newValue = change.after.val();
        const { latitude, longitude } = newValue;

        if (latitude && longitude) {
            const geohash = encodeGeohash(latitude, longitude, 6);
            // Use change.after.ref to update the same reference
            return change.after.ref.update({
                geohash: geohash
            });
        }
        return null;
    });
