const base32 = '0123456789bcdefghjkmnpqrstuvwxyz';

function encodeGeohash(latitude, longitude, precision = 6) {
    let latRange = [-90.0, 90.0];
    let lngRange = [-180.0, 180.0];
    let geohash = '';
    let isEvenBit = true;
    let bit = 0;
    let ch = 0;

    while (geohash.length < precision) {
        if (isEvenBit) {
            const mid = (lngRange[0] + lngRange[1]) / 2;
            if (longitude > mid) {
                ch |= 1 << (4 - bit);
                lngRange[0] = mid;
            } else {
                lngRange[1] = mid;
            }
        } else {
            const mid = (latRange[0] + latRange[1]) / 2;
            if (latitude > mid) {
                ch |= 1 << (4 - bit);
                latRange[0] = mid;
            } else {
                latRange[1] = mid;
            }
        }

        isEvenBit = !isEvenBit;

        if (++bit === 5) {
            geohash += base32[ch];
            bit = 0;
            ch = 0;
        }
    }

    return geohash;
}

module.exports = { encodeGeohash };
