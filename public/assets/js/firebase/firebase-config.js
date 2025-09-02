// Import Firebase SDK for modular approach (v9+)
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging.js";

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
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Request notification permission
const requestNotificationPermission = async () => {
    try {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            return await getToken(messaging, {
                vapidKey: "BNQHK6ApjJvOlt005IIB5V0qopj14RbCKd5Yk3yrclG97uWfslqTl20SfX3W-iC_8TbCDSYoyApzSYp3PcLM0-8"
            });
        }
        throw new Error('Permission not granted');
    } catch (error) {
        console.error('Error getting permission:', error);
        throw error;
    }
};

// Send token to server
const sendTokenToServer = async (token) => {
    try {
        const response = await fetch("/update-device-token", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                token: token,
                platform: "web"
            })
        });
        localStorage.setItem("isCmfToken", "true");
        return await response.json();
    } catch (error) {
        console.error('Error sending token:', error);
        throw error;
    }
};

// Initialize Firebase and messaging
const initializeFirebase = async () => {
    try {
        if ('serviceWorker' in navigator) {
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            console.log('Service Worker registered');

            const token = await requestNotificationPermission();
            await sendTokenToServer(token);

            onMessage(messaging, (payload) => {
                console.log('Foreground message:', payload);
                if (window.handleFirebaseMessage) {
                    window.handleFirebaseMessage(payload);
                } else {
                    console.warn('Notification handler not found');
                }
            });
        }
    } catch (error) {
        console.error('Firebase initialization error:', error);
    }
};

export { app, messaging, initializeFirebase };
