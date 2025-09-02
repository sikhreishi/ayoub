// public/assets/js/firebase/firebase-config.js
const firebaseConfig = {
    apiKey: "AIzaSyDwH1kfiDSLvjI4V4UxLqZQnIyGH87MBzw",
    authDomain: "waddini-ccbc7.firebaseapp.com",
    databaseURL: "https://waddini-ccbc7-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "waddini-ccbc7",
    storageBucket: "waddini-ccbc7.firebasestorage.app",
    messagingSenderId: "823593320488",
    appId: "1:823593320488:web:d17d3a8ec271fd6a85b51d",
    measurementId: "G-8KDJ0T6YXF"
};

// Initialize Firebase
const app = firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging(app);

// Request notification permission
function requestNotificationPermission() {
    return new Promise((resolve, reject) => {
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                getToken();
            } else {
                reject(new Error('Permission not granted'));
            }
        }).catch(reject);
    });
}

// Get FCM token
function getToken() {
    return messaging.getToken({
        vapidKey: "BNQHK6ApjJvOlt005IIB5V0qopj14RbCKd5Yk3yrclG97uWfslqTl20SfX3W-iC_8TbCDSYoyApzSYp3PcLM0-8"
    }).then((currentToken) => {
        if (currentToken) {
            return sendTokenToServer(currentToken);
        } else {
            console.log('No registration token available.');
        }
    }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
    });
}

// Send token to server
function sendTokenToServer(token) {
    return $.ajax({
        url: "/update-device-token",
        method: "PUT",
        data: {
            token: token,
            platform: "web",
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    }).then((response) => {
        localStorage.setItem("isCmfToken", true);
        return response;
    });
}

// Initialize Firebase Messaging
function initializeFirebaseMessaging() {
    // Register service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then((registration) => {
                console.log('Service Worker registered');

                // Request notification permission
                return requestNotificationPermission();
            })
            .catch((err) => {
                console.log('Service Worker registration failed:', err);
            });
    }

    // Handle foreground messages
    messaging.onMessage((payload) => {
        console.log('Foreground message received:', payload);
        // Handle foreground notifications
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeFirebaseMessaging);
