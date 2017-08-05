package com.panaceasoft.mokets.utilities;

import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Intent;
import android.support.v4.app.NotificationCompat;

import com.google.firebase.messaging.RemoteMessage;
import com.panaceasoft.mokets.R;
import com.panaceasoft.mokets.activities.MainActivity;
import com.panaceasoft.mokets.fragments.NotificationFragment;

/**
 * Created by rafeldo.xyz
 * Contact Email : rafeldo29@@gmail.com
 */
public class FirebaseMessagingService extends com.google.firebase.messaging.FirebaseMessagingService {
    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        /* Important : Please don't change this "message" because if you change this, need to update at PHP.  */
        showNotification(remoteMessage.getData().get("message"));
    }

    private void showNotification(String message) {

        if(Utils.activity != null){
            if(Utils.activity.fragment != null) {
                if (Utils.activity.fragment instanceof NotificationFragment) {
                    Utils.activity.savePushMessage(message);
                    Utils.activity.refreshNotification();
                }
            }
        }

        Intent i = new Intent(this,MainActivity.class);
        i.putExtra("msg", message);
        String displayMessage = "";

        if(message != null) {
            i.putExtra("show_noti", true);
            displayMessage = "You've received new message.";
        } else {
            i.putExtra("show_noti", false);
            displayMessage = "Welcome from Mokets";
        }

        i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);

        PendingIntent pendingIntent = PendingIntent.getActivity(this,0,i,PendingIntent.FLAG_UPDATE_CURRENT);

        NotificationCompat.Builder builder = new NotificationCompat.Builder(this)
                .setAutoCancel(true)
                .setContentTitle("Alert")
                .setContentText(displayMessage)
                .setSmallIcon(R.drawable.ic_notifications_white)
                .setContentIntent(pendingIntent);

        // Set Vibrate, Sound and Light
        int defaults = 0;
        defaults = defaults | Notification.DEFAULT_LIGHTS;
        defaults = defaults | Notification.DEFAULT_VIBRATE;
        defaults = defaults | Notification.DEFAULT_SOUND;

        builder.setDefaults(defaults);
        // Set the content for Notification
        builder.setContentText(displayMessage);
        // Set autocancel
        builder.setAutoCancel(true);

        NotificationManager manager = (NotificationManager) getSystemService(NOTIFICATION_SERVICE);

        manager.notify(0,builder.build());
    }
}
