import { Capacitor } from '@capacitor/core'
import { PushNotifications } from '@capacitor/push-notifications'

const isNative =
    typeof window !== 'undefined' &&
    Capacitor.isNativePlatform()

if (isNative) {
    PushNotifications.requestPermissions().then(result => {
        if (result.receive === 'granted') {
            PushNotifications.register()
        }
    })

    PushNotifications.addListener('registration', token => {
        console.log('Push registration token:', token.value)
    })

    PushNotifications.addListener('registrationError', error => {
        console.error('Push registration error:', error)
    })

    PushNotifications.addListener('pushNotificationReceived', notification => {
        console.log('Push received:', notification)
    })

    PushNotifications.addListener('pushNotificationActionPerformed', action => {
        console.log('Push action performed:', action)
    })
}
